<?php

namespace MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuToCategories
 *
 * @ORM\Table(name="menutocategories")
 * @ORM\Entity(repositoryClass="MenuBundle\Repository\MenuToCategoriesRepository")
 */
class MenuToCategories
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
     * @var int
     *
     * @ORM\Column(name="place", type="smallint")
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="categories")
     */
    protected $menu;
    
    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class)
     */
    protected $category;
    

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
     * Set place
     *
     * @param integer $place
     *
     * @return MenuToCategories
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return int
     */
    public function getPlace()
    {
        return $this->place;
    }
}

