<?php

namespace App\Entity\colmenar;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * FutUEquiposImagenes
 *
 * @ORM\Table(name="fut_u_equipos_imagenes", indexes={@ORM\Index(name="id_imagen", columns={"id_imagen"}), @ORM\Index(name="id_equipo", columns={"id_equipo"})})
 * @ORM\Entity
 */
class FutUEquiposImagenes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_equipos_imagenes", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEquiposImagenes;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="temporada", type="string", length=15, nullable=true)
     */
    private $temporada;

    /**
     * @var \FutTEquipos
     *
     * @ORM\ManyToOne(targetEntity="FutTEquipos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_equipo", referencedColumnName="id_equipo")
     * })
     */
    private $idEquipo;

    /**
     * @var \FutTImagenes
     *
     * @ORM\ManyToOne(targetEntity="FutTImagenes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagen", referencedColumnName="id_imagen")
     * })
     */
    private $idImagen;

    public function getIdEquiposImagenes(): ?int
    {
        return $this->idEquiposImagenes;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTemporada(): ?string
    {
        return $this->temporada;
    }

    public function setTemporada(?string $temporada): self
    {
        $this->temporada = $temporada;

        return $this;
    }

    public function getIdEquipo(): ?FutTEquipos
    {
        return $this->idEquipo;
    }

    public function setIdEquipo(?FutTEquipos $idEquipo): self
    {
        $this->idEquipo = $idEquipo;

        return $this;
    }

    public function getIdImagen(): ?FutTImagenes
    {
        return $this->idImagen;
    }

    public function setIdImagen(?FutTImagenes $idImagen): self
    {
        $this->idImagen = $idImagen;

        return $this;
    }


}
