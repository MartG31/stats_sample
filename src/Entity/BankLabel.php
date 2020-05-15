<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankLabel
 *
 * @ORM\Table(name="bank_label", indexes = {
        @ORM\Index(name="fk_bank_label_user", columns={"user_id"}),
        @ORM\Index(name="fk_bank_label_cat", columns={"cat_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BankLabelRepository")
 */

class BankLabel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

        /**
     * @var \BankCategory
     *
     * @ORM\ManyToOne(targetEntity="BankCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cat_id", referencedColumnName="id")
     * })
     */
    private $cat;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=120, nullable=false)
     */
    private $label;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCat(): ?BankCategory
    {
        return $this->cat;
    }

    public function setCat(?BankCategory $cat): self
    {
        $this->cat = $cat;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
