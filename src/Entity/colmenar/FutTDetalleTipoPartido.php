<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTDetalleTipoPartido
 *
 * @ORM\Table(name="fut_t_detalle_tipo_partido", indexes={@ORM\Index(name="id_tipo_partido", columns={"id_tipo_partido"})})
 * @ORM\Entity
 */
class FutTDetalleTipoPartido
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_detalle_tipo_partido", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idDetalleTipoPartido;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_detalle", type="string", length=80, nullable=false)
     */
    private $nombreDetalle = '';

    /**
     * @var \FutTTipoPartido
     *
     * @ORM\ManyToOne(targetEntity="FutTTipoPartido")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_partido", referencedColumnName="id_tipo_partido")
     * })
     */
    private $idTipoPartido;

    public function getIdDetalleTipoPartido(): ?int
    {
        return $this->idDetalleTipoPartido;
    }

    public function getNombreDetalle(): ?string
    {
        return $this->nombreDetalle;
    }

    public function setNombreDetalle(string $nombreDetalle): self
    {
        $this->nombreDetalle = $nombreDetalle;

        return $this;
    }

    public function getIdTipoPartido(): ?FutTTipoPartido
    {
        return $this->idTipoPartido;
    }

    public function setIdTipoPartido(?FutTTipoPartido $idTipoPartido): self
    {
        $this->idTipoPartido = $idTipoPartido;

        return $this;
    }


}
