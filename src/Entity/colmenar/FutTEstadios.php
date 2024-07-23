<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTEstadios
 *
 * @ORM\Table(name="fut_t_estadios")
 * @ORM\Entity
 */
class FutTEstadios
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_estadio", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEstadio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_estadio", type="string", length=80, nullable=true)
     */
    private $nombreEstadio;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=200, nullable=false)
     */
    private $direccion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codigo_fed", type="integer", nullable=true)
     */
    private $codigoFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_fed", type="string", length=200, nullable=true)
     */
    private $nombreFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion_fed", type="string", length=200, nullable=true)
     */
    private $direccionFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo_postal_fed", type="string", length=10, nullable=true)
     */
    private $codigoPostalFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="provincia_fed", type="string", length=30, nullable=true)
     */
    private $provinciaFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="localidad_fed", type="string", length=50, nullable=true)
     */
    private $localidadFed;

    public function getIdEstadio(): ?int
    {
        return $this->idEstadio;
    }

    public function getNombreEstadio(): ?string
    {
        return $this->nombreEstadio;
    }

    public function setNombreEstadio(?string $nombreEstadio): self
    {
        $this->nombreEstadio = $nombreEstadio;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getCodigoFed(): ?int
    {
        return $this->codigoFed;
    }

    public function setCodigoFed(?int $codigoFed): self
    {
        $this->codigoFed = $codigoFed;

        return $this;
    }

    public function getNombreFed(): ?string
    {
        return $this->nombreFed;
    }

    public function setNombreFed(?string $nombreFed): self
    {
        $this->nombreFed = $nombreFed;

        return $this;
    }

    public function getDireccionFed(): ?string
    {
        return $this->direccionFed;
    }

    public function setDireccionFed(?string $direccionFed): self
    {
        $this->direccionFed = $direccionFed;

        return $this;
    }

    public function getCodigoPostalFed(): ?string
    {
        return $this->codigoPostalFed;
    }

    public function setCodigoPostalFed(?string $codigoPostalFed): self
    {
        $this->codigoPostalFed = $codigoPostalFed;

        return $this;
    }

    public function getProvinciaFed(): ?string
    {
        return $this->provinciaFed;
    }

    public function setProvinciaFed(?string $provinciaFed): self
    {
        $this->provinciaFed = $provinciaFed;

        return $this;
    }

    public function getLocalidadFed(): ?string
    {
        return $this->localidadFed;
    }

    public function setLocalidadFed(?string $localidadFed): self
    {
        $this->localidadFed = $localidadFed;

        return $this;
    }


}
