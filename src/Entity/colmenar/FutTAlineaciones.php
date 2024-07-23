<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTAlineaciones
 *
 * @ORM\Table(name="fut_t_alineaciones", indexes={@ORM\Index(name="id_equipo", columns={"id_equipo"})})
 * @ORM\Entity
 */
class FutTAlineaciones
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_alineacion", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAlineacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="temporada", type="string", length=10, nullable=true)
     */
    private $temporada;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ruta", type="string", length=255, nullable=true)
     */
    private $ruta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ruta_imagen_pequena", type="string", length=255, nullable=true)
     */
    private $rutaImagenPequena;

    /**
     * @var \FutTEquipos
     *
     * @ORM\ManyToOne(targetEntity="FutTEquipos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_equipo", referencedColumnName="id_equipo")
     * })
     */
    private $idEquipo;

    public function getIdAlineacion(): ?int
    {
        return $this->idAlineacion;
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

    public function getRuta(): ?string
    {
        return $this->ruta;
    }

    public function setRuta(?string $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }

    public function getRutaImagenPequena(): ?string
    {
        return $this->rutaImagenPequena;
    }

    public function setRutaImagenPequena(?string $rutaImagenPequena): self
    {
        $this->rutaImagenPequena = $rutaImagenPequena;

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


}
