<?php

namespace MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany as OneToMany;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity(repositoryClass="MenuBundle\Repository\CategorieRepository")
 */
class Categorie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=50, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=255, nullable=true)
     */
    private $route;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    private $isEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * Une catégorie peut contenir n catégories
     * 
     * @OneToMany(targetEntity="Categorie", mappedBy="parent")
     */
    private $children;

    /**
     * Plusieurs Categories peuvent avoir une catéogie parent
     * 
     * @ManyToOne(targetEntity="Categorie", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=\ContentBundle\Entity\CategorieToArticles::class, mappedBy="category")
     */
    private $articles;
    
    public function __construct() {
    	$this->children = new ArrayCollection();
    	$this->articles = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Categorie
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set route
     *
     * @param string $route
     *
     * @return Categorie
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     *
     * @return Categorie
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Categorie
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return JsonDecode
     */
    public function getContent()
    {
    	
    	if ($this->content) {
    		$json = new JsonDecode(true);
    		return $json->decode($this->content, JsonEncoder::FORMAT);
    	}
    }
    
    /**
     * Retourne la liste des catégories filles
     * @return ArrayCollection
     */
    public function getChildren() {
    	return $this->children;
    }
    

    public function childrenToArray() {
		$datas = [];
		
		if (($children = $this->getChildren())) {
			foreach ($children as $child) {
				if ($child->getIsEnabled()) {
					$datas[] = [
						"id" => $child->getId(),
						"slug" => $child->getSlug(),
						"route" => $child->getRoute(),
						"content" => $child->getContent(),
						"nodes" => $child->childrenToArray()
					];
				}
			}
		}
    }
    
    /**
     * Ajoute un article lié à la catégorie courante
     * @param \ContentBundle\Entity\Article $article
     * @return \MenuBundle\Entity\Categorie
     */
    public function addArticle(\ContentBundle\Entity\Article $article): \MenuBundle\Entity\Categorie {
    	$this->articles[] = $article;
    	return $this;
    }
    
    /**
     * Retourne la liste des articles associées à la catégorie courante
     * @return ArrayCollection
     */
    public function getArticles() {
    	return $this->articles;
    }
    
    /**
     * Retourne le parent de la catégorie courante
     * @return \MenuBundle\Categorie
     */
    public function getParent() {
    	return $this->parent;
    }
}

