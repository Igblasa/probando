<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTImagenesCronicas
 *
 * @ORM\Table(name="fut_t_imagenes_cronicas", indexes={@ORM\Index(name="id_cronica", columns={"id_cronica"})})
 * @ORM\Entity
 */
class FutTImagenesCronicas
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
     * @var \FutTCronicas
     *
     * @ORM\ManyToOne(targetEntity="FutTCronicas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cronica", referencedColumnName="id_cronica")
     * })
     */
    private $idCronica;

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

    public function getIdCronica(): ?FutTCronicas
    {
        return $this->idCronica;
    }

    public function setIdCronica(?FutTCronicas $idCronica): self
    {
        $this->idCronica = $idCronica;

        return $this;
    }


}
