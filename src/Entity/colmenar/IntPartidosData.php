<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="int_partidosdata")
 * @ORM\Entity
 */

class IntPartidosData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $equipo;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $rival;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $resultadoLocal;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $resultadoVisitante;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $horario;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $local;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $nombreEstadio;
    
    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $idPartido;

    // Getters y setters para los campos

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getEquipo(): ?string
    {
        return $this->equipo;
    }

    public function setEquipo(string $equipo): self
    {
        $this->equipo = $equipo;
        return $this;
    }

    public function getRival(): ?string
    {
        return $this->rival;
    }

    public function setRival(string $rival): self
    {
        $this->rival = $rival;
        return $this;
    }

    public function getResultadoLocal(): ?string
    {
        return $this->resultadoLocal;
    }

    public function setResultadoLocal(?string $resultadoLocal): self
    {
        $this->resultadoLocal = $resultadoLocal;
        return $this;
    }

    public function getResultadoVisitante(): ?string
    {
        return $this->resultadoVisitante;
    }

    public function setResultadoVisitante(?string $resultadoVisitante): self
    {
        $this->resultadoVisitante = $resultadoVisitante;
        return $this;
    }

    public function getHorario(): ?\DateTimeInterface
    {
        return $this->horario;
    }

    public function setHorario(?\DateTimeInterface $horario): self
    {
        $this->horario = $horario;
        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(string $local): self
    {
        $this->local = $local;
        return $this;
    }

    public function getNombreEstadio(): ?string
    {
        return $this->nombreEstadio;
    }

    public function setNombreEstadio(?string $nombreEstadio): self
    {
        $this->nombreEstadio = $nombreEstadio;
        return $this;
    }
    
    public function getIdPartido(): ?string
    {
        return $this->idPartido;
    }

    public function setIdPartido(?string $idPartido): self
    {
        $this->idPartido = $idPartido;
        return $this;
    }
}
