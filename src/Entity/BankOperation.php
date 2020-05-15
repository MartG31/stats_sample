<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankOperation
 *
 * @ORM\Table(name="bank_operation", indexes = {
        @ORM\Index(name="fk_bank_operation_user", columns={"user_id"}),
        @ORM\Index(name="fk_bank_operation_bank_account", columns={"acc_id"}),
        @ORM\Index(name="fk_bank_operation_related_bank_account", columns={"rel_acc_id"}),
        @ORM\Index(name="fk_bank_operation_bank_label", columns={"lab_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BankOperationRepository")
 */

class BankOperation
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
     * @var \BankAccount
     *
     * @ORM\ManyToOne(targetEntity="BankAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="acc_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $acc;

    /**
     * @var \BankAccount
     *
     * @ORM\ManyToOne(targetEntity="BankAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rel_acc_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $rel_acc;

    /**
     * @var \BankLabel
     *
     * @ORM\ManyToOne(targetEntity="BankLabel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lab_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $lab;

    /**
     * @var string
     *
     * @ORM\Column(name="ref", type="string", length=50, nullable=false)
     */
    private $ref;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", length=50, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_compta", type="date", nullable=false)
     */
    private $dateCompta;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCompta(): ?\DateTimeInterface
    {
        return $this->dateCompta;
    }

    public function setDateCompta(\DateTimeInterface $dateCompta): self
    {
        $this->dateCompta = $dateCompta;

        return $this;
    }

    public function getAcc(): ?BankAccount
    {
        return $this->acc;
    }

    public function setAcc(?BankAccount $acc): self
    {
        $this->acc = $acc;

        return $this;
    }

    public function getRelAcc(): ?BankAccount
    {
        return $this->rel_acc;
    }

    public function setRelAcc(?BankAccount $rel_acc): self
    {
        $this->rel_acc = $rel_acc;

        return $this;
    }

    public function getLab(): ?BankLabel
    {
        return $this->lab;
    }

    public function setLab(?BankLabel $lab): self
    {
        $this->lab = $lab;

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
