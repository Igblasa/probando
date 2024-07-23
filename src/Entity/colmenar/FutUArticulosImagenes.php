<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutUArticulosImagenes
 *
 * @ORM\Table(name="fut_u_articulos_imagenes", indexes={@ORM\Index(name="id_imagen", columns={"id_imagen"}), @ORM\Index(name="id_articulo", columns={"id_articulo"})})
 * @ORM\Entity
 */
class FutUArticulosImagenes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_articulos_imagenes", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idArticulosImagenes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="principal", type="string", length=2, nullable=true)
     */
    private $principal;

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer", nullable=false, options={"default"="100"})
     */
    private $orden = 100;

    /**
     * @var \FutTArticulos
     *
     * @ORM\ManyToOne(targetEntity="FutTArticulos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_articulo", referencedColumnName="id_articulo")
     * })
     */
    private $idArticulo;

    /**
     * @var \FutTImagenes
     *
     * @ORM\ManyToOne(targetEntity="FutTImagenes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_imagen", referencedColumnName="id_imagen")
     * })
     */
    private $idImagen;

    public function getIdArticulosImagenes(): ?int
    {
        return $this->idArticulosImagenes;
    }

    public function getPrincipal(): ?string
    {
        return $this->principal;
    }

    public function setPrincipal(?string $principal): self
    {
        $this->principal = $principal;

        return $this;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }

    public function setOrden(int $orden): self
    {
        $this->orden = $orden;

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
