<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTRivales
 *
 * @ORM\Table(name="fut_t_rivales")
 * @ORM\Entity
 */
class FutTRivales
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_rival", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRival;

    /**
     * @var string
     *
     * @ORM\Column(name="rival", type="string", length=50, nullable=false)
     */
    private $rival = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="camiseta", type="string", length=40, nullable=true)
     */
    private $camiseta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pantalon", type="string", length=40, nullable=true)
     */
    private $pantalon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="medias", type="string", length=40, nullable=true)
     */
    private $medias;

    /**
     * @var string
     *
     * @ORM\Column(name="comprobada_equipacion", type="string", length=20, nullable=false, options={"default"="NO-19-20"})
     */
    private $comprobadaEquipacion = 'NO-19-20';

    /**
     * @var int|null
     *
     * @ORM\Column(name="cod_eq_fed", type="integer", nullable=true)
     */
    private $codEqFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_eq_fed", type="string", length=100, nullable=true)
     */
    private $nombreEqFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="localidad_fed", type="string", length=50, nullable=true)
     */
    private $localidadFed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="provincia_fed", type="string", length=40, nullable=true)
     */
    private $provinciaFed;

    public function getIdRival(): ?int
    {
        return $this->idRival;
    }

    public function getRival(): ?string
    {
        return $this->rival;
    }

    public function setRival(string $rival): self
    {
        $this->rival = $rival;

        return $this;
    }

    public function getCamiseta(): ?string
    {
        return $this->camiseta;
    }

    public function setCamiseta(?string $camiseta): self
    {
        $this->camiseta = $camiseta;

        return $this;
    }

    public function getPantalon(): ?string
    {
        return $this->pantalon;
    }

    public function setPantalon(?string $pantalon): self
    {
        $this->pantalon = $pantalon;

        return $this;
    }

    public function getMedias(): ?string
    {
        return $this->medias;
    }

    public function setMedias(?string $medias): self
    {
        $this->medias = $medias;

        return $this;
    }

    public function getComprobadaEquipacion(): ?string
    {
        return $this->comprobadaEquipacion;
    }

    public function setComprobadaEquipacion(string $comprobadaEquipacion): self
    {
        $this->comprobadaEquipacion = $comprobadaEquipacion;

        return $this;
    }

    public function getCodEqFed(): ?int
    {
        return $this->codEqFed;
    }

    public function setCodEqFed(?int $codEqFed): self
    {
        $this->codEqFed = $codEqFed;

        return $this;
    }

    public function getNombreEqFed(): ?string
    {
        return $this->nombreEqFed;
    }

    public function setNombreEqFed(?string $nombreEqFed): self
    {
        $this->nombreEqFed = $nombreEqFed;

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

    public function getProvinciaFed(): ?string
    {
        return $this->provinciaFed;
    }

    public function setProvinciaFed(?string $provinciaFed): self
    {
        $this->provinciaFed = $provinciaFed;

        return $this;
    }


}
