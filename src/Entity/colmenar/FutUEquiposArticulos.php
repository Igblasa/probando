<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutUEquiposArticulos
 *
 * @ORM\Table(name="fut_u_equipos_articulos", indexes={@ORM\Index(name="id_articulo", columns={"id_articulo"}), @ORM\Index(name="id_equipo", columns={"id_equipo"})})
 * @ORM\Entity
 */
class FutUEquiposArticulos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_equipos_articulos", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEquiposArticulos;

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
     * @var \FutTArticulos
     *
     * @ORM\ManyToOne(targetEntity="FutTArticulos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_articulo", referencedColumnName="id_articulo")
     * })
     */
    private $idArticulo;

    public function getIdEquiposArticulos(): ?int
    {
        return $this->idEquiposArticulos;
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

    public function getIdArticulo(): ?FutTArticulos
    {
        return $this->idArticulo;
    }

    public function setIdArticulo(?FutTArticulos $idArticulo): self
    {
        $this->idArticulo = $idArticulo;

        return $this;
    }


}
