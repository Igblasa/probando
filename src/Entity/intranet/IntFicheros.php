<?php

// src/Entity/Fichero.php
namespace App\Entity\intranet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="int_ficheros")
 * @ORM\Entity(repositoryClass="App\Repository\FICHEROS\FicherosRepository")
 */
class IntFicheros
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $extension;

    /**
     * @ORM\Column(type="integer")
     */
    private $tamaño;
    
    /**
     * @ORM\Column(type="string", length=250)
     */
    private $ruta;

    // Getters y setters para los campos

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getTamaño(): ?int
    {
        return $this->tamaño;
    }

    public function setTamaño(int $tamaño): self
    {
        $this->tamaño = $tamaño;

        return $this;
    }

    public function getRuta(): ?string
    {
        return $this->ruta;
    }

    public function setRuta(string $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }
}
