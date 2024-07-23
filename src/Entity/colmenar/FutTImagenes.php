<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTImagenes
 *
 * @ORM\Table(name="fut_t_imagenes")
 * @ORM\Entity
 */
class FutTImagenes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_imagen", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idImagen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ruta", type="string", length=255, nullable=true)
     */
    private $ruta;

    /**
     * @var int
     *
     * @ORM\Column(name="imagen_base", type="integer", nullable=false)
     */
    private $imagenBase = '0';

    public function getIdImagen(): ?int
    {
        return $this->idImagen;
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

    public function getImagenBase(): ?int
    {
        return $this->imagenBase;
    }

    public function setImagenBase(int $imagenBase): self
    {
        $this->imagenBase = $imagenBase;

        return $this;
    }


}
