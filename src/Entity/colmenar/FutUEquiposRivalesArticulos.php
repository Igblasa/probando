<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutUEquiposRivalesArticulos
 *
 * @ORM\Table(name="fut_u_equipos_rivales_articulos", uniqueConstraints={@ORM\UniqueConstraint(name="id_articulo", columns={"id_articulo"}), @ORM\UniqueConstraint(name="id_partido", columns={"id_partido"})})
 * @ORM\Entity
 */
class FutUEquiposRivalesArticulos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_equipos_rivales_articulos", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEquiposRivalesArticulos;

    /**
     * @var \FutUEquiposRivales
     *
     * @ORM\ManyToOne(targetEntity="FutUEquiposRivales")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_partido", referencedColumnName="id_partido")
     * })
     */
    private $idPartido;

    /**
     * @var \FutTArticulos
     *
     * @ORM\ManyToOne(targetEntity="FutTArticulos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_articulo", referencedColumnName="id_articulo")
     * })
     */
    private $idArticulo;

    public function getIdEquiposRivalesArticulos(): ?int
    {
        return $this->idEquiposRivalesArticulos;
    }

    public function getIdPartido(): ?FutUEquiposRivales
    {
        return $this->idPartido;
    }

    public function setIdPartido(?FutUEquiposRivales $idPartido): self
    {
        $this->idPartido = $idPartido;

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
