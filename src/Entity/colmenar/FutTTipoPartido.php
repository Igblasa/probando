<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTTipoPartido
 *
 * @ORM\Table(name="fut_t_tipo_partido")
 * @ORM\Entity
 */
class FutTTipoPartido
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_tipo_partido", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTipoPartido;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_tipo_partido", type="string", length=30, nullable=false)
     */
    private $nombreTipoPartido = '';

    public function getIdTipoPartido(): ?int
    {
        return $this->idTipoPartido;
    }

    public function getNombreTipoPartido(): ?string
    {
        return $this->nombreTipoPartido;
    }

    public function setNombreTipoPartido(string $nombreTipoPartido): self
    {
        $this->nombreTipoPartido = $nombreTipoPartido;

        return $this;
    }


}
