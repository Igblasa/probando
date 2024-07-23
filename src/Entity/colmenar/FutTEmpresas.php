<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTEmpresas
 *
 * @ORM\Table(name="fut_t_empresas")
 * @ORM\Entity
 */
class FutTEmpresas
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEmpresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="empresa", type="string", length=50, nullable=true)
     */
    private $empresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion", type="string", length=50, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ciudad", type="string", length=50, nullable=true)
     */
    private $ciudad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefono", type="string", length=9, nullable=true)
     */
    private $telefono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="web", type="string", length=100, nullable=true)
     */
    private $web;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mail", type="string", length=50, nullable=true)
     */
    private $mail;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tipo_empresa", type="string", length=20, nullable=true)
     */
    private $tipoEmpresa;

    /**
     * @var string|null
     *
     * @ORM\Column(name="imagen", type="string", length=150, nullable=true)
     */
    private $imagen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mostrar", type="string", length=2, nullable=true)
     */
    private $mostrar;

    /**
     * @var int|null
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    private $orden = '0';

    public function getIdEmpresa(): ?int
    {
        return $this->idEmpresa;
    }

    public function getEmpresa(): ?string
    {
        return $this->empresa;
    }

    public function setEmpresa(?string $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getCiudad(): ?string
    {
        return $this->ciudad;
    }

    public function setCiudad(?string $ciudad): self
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): self
    {
        $this->web = $web;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getTipoEmpresa(): ?string
    {
        return $this->tipoEmpresa;
    }

    public function setTipoEmpresa(?string $tipoEmpresa): self
    {
        $this->tipoEmpresa = $tipoEmpresa;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getMostrar(): ?string
    {
        return $this->mostrar;
    }

    public function setMostrar(?string $mostrar): self
    {
        $this->mostrar = $mostrar;

        return $this;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }

    public function setOrden(?int $orden): self
    {
        $this->orden = $orden;

        return $this;
    }


}
