<?php

namespace App\Entity\colmenar;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * FutTArticulos
 *
 * @ORM\Table(name="fut_t_articulos", indexes={@ORM\Index(name="id_articulo", columns={"id_articulo"})})
 * @ORM\Entity
 */
class FutTArticulos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_articulo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idArticulo;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=200, nullable=false)
     */
    private $titulo = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="texto", type="text", length=0, nullable=true)
     */
    private $texto;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=20, nullable=false)
     */
    private $tipo = '';

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer", nullable=false)
     */
    private $orden = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="mostrar_general", type="integer", nullable=false, options={"default"="1"})
     */
    private $mostrarGeneral = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="mostrar_carrusel", type="integer", nullable=false, options={"default"="1"})
     */
    private $mostrarCarrusel = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="mostrar_imagen", type="integer", nullable=false, options={"default"="1"})
     */
    private $mostrarImagen = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="mostrar_historico", type="integer", nullable=false)
     */
    private $mostrarHistorico = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="url_video_youtube", type="string", length=300, nullable=false)
     */
    private $urlVideoYoutube;

    public function getIdArticulo(): ?int
    {
        return $this->idArticulo;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): self
    {
        $this->texto = $texto;

        return $this;
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

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

    public function getMostrarGeneral(): ?int
    {
        return $this->mostrarGeneral;
    }

    public function setMostrarGeneral(int $mostrarGeneral): self
    {
        $this->mostrarGeneral = $mostrarGeneral;

        return $this;
    }

    public function getMostrarCarrusel(): ?int
    {
        return $this->mostrarCarrusel;
    }

    public function setMostrarCarrusel(int $mostrarCarrusel): self
    {
        $this->mostrarCarrusel = $mostrarCarrusel;

        return $this;
    }

    public function getMostrarImagen(): ?int
    {
        return $this->mostrarImagen;
    }

    public function setMostrarImagen(int $mostrarImagen): self
    {
        $this->mostrarImagen = $mostrarImagen;

        return $this;
    }

    public function getMostrarHistorico(): ?int
    {
        return $this->mostrarHistorico;
    }

    public function setMostrarHistorico(int $mostrarHistorico): self
    {
        $this->mostrarHistorico = $mostrarHistorico;

        return $this;
    }

    public function getUrlVideoYoutube(): ?string
    {
        return $this->urlVideoYoutube;
    }

    public function setUrlVideoYoutube(string $urlVideoYoutube): self
    {
        $this->urlVideoYoutube = $urlVideoYoutube;

        return $this;
    }


}
