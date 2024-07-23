<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="int_partidos_liga_csv")
 * @ORM\Entity()
 */
class IntPartidosLigaCsv
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id_partidos_liga_csv;
    
    /**
     * @ORM\Column(name="fecha",type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(name="hora",type="time", nullable=true)
     */
    private $hora;

    /**
     * @ORM\Column(name="competicion",type="string", length=100, nullable=true)
     */
    private $competicion;

    /**
     * @ORM\Column(name="grupo",type="string", length=100, nullable=true)
     */
    private $grupo;

    /**
     * @ORM\Column(name="club_casa",type="integer", nullable=true)
     */
    private $clubCasa;

    /**
     * @ORM\Column(name="club_visitante",type="integer", nullable=true)
     */
    private $clubVisitante;

    /**
     * @ORM\Column(name="nombre_club_casa",type="string", length=150, nullable=true)
     */
    private $nombreClubCasa;

    /**
     * @ORM\Column(name="nombre_club_visitante",type="string", length=150, nullable=true)
     */
    private $nombreClubVisitante;

    /**
     * @ORM\Column(name="equipo_casa",type="string", length=150, nullable=true)
     */
    private $equipoCasa;

    /**
     * @ORM\Column(name="equipo_visitante",type="string", length=150, nullable=true)
     */
    private $equipoVisitante;

    /**
     * @ORM\Column(name="campo",type="string", length=250, nullable=true)
     */
    private $campo;

    /**
     * @ORM\Column(name="direccion_campo",type="string", length=150, nullable=true)
     */
    private $direccionCampo;

    /**
     * @ORM\Column(name="jornada",type="string", length=10, nullable=true)
     */
    private $jornada;

    /**
     * @ORM\Column(name="resultado",type="string", length=10, nullable=true)
     */
    private $resultado;

    /**
     * @ORM\Column(name="codigoPartido",type="integer", nullable=true)
     */
    private $codigoPartido;

    /**
     * @ORM\Column(name="arbitro",type="string", length=200, nullable=true)
     */
    private $arbitro;

    // Métodos Getter

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function getHora()
    {
        return $this->hora;
    }

    public function getCompeticion(): ?string
    {
        return $this->competicion;
    }

    public function getGrupo(): ?string
    {
        return $this->grupo;
    }

    public function getClubCasa(): ?int
    {
        return $this->clubCasa;
    }

    public function getClubVisitante(): ?int
    {
        return $this->clubVisitante;
    }

    public function getNombreClubCasa(): ?string
    {
        return $this->nombreClubCasa;
    }

    public function getNombreClubVisitante(): ?string
    {
        return $this->nombreClubVisitante;
    }

    public function getEquipoCasa(): ?string
    {
        return $this->equipoCasa;
    }

    public function getEquipoVisitante(): ?string
    {
        return $this->equipoVisitante;
    }

    public function getCampo(): ?string
    {
        return $this->campo;
    }

    public function getDireccionCampo(): ?string
    {
        return $this->direccionCampo;
    }

    public function getJornada(): ?string
    {
        return $this->jornada;
    }

    public function getResultado(): ?string
    {
        return $this->resultado;
    }

    public function getCodigoPartido(): ?int
    {
        return $this->codigoPartido;
    }

    public function getArbitro(): ?string
    {
        return $this->arbitro;
    }

    // Métodos Setter

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function setHora($hora): self
    {
        $this->hora = $hora;

        return $this;
    }

    public function setCompeticion(string $competicion): self
    {
        $this->competicion = $competicion;

        return $this;
    }

    public function setGrupo(string $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }

    public function setClubCasa(int $clubCasa): self
    {
        $this->clubCasa = $clubCasa;

        return $this;
    }

    public function setClubVisitante(int $clubVisitante): self
    {
        $this->clubVisitante = $clubVisitante;

        return $this;
    }

    public function setNombreClubCasa(string $nombreClubCasa): self
    {
        $this->nombreClubCasa = $nombreClubCasa;

        return $this;
    }

    public function setNombreClubVisitante(string $nombreClubVisitante): self
    {
        $this->nombreClubVisitante = $nombreClubVisitante;

        return $this;
    }

    public function setEquipoCasa(string $equipoCasa): self
    {
        $this->equipoCasa = $equipoCasa;

        return $this;
    }

    public function setEquipoVisitante(string $equipoVisitante): self
    {
        $this->equipoVisitante = $equipoVisitante;

        return $this;
    }

    public function setCampo(string $campo): self
    {
        $this->campo = $campo;

        return $this;
    }

    public function setDireccionCampo(string $direccionCampo): self
    {
        $this->direccionCampo = $direccionCampo;

        return $this;
    }

    public function setJornada(string $jornada): self
    {
        $this->jornada = $jornada;

        return $this;
    }

    public function setResultado(string $resultado): self
    {
        $this->resultado = $resultado;

        return $this;
    }

    public function setCodigoPartido(int $codigoPartido): self
    {
        $this->codigoPartido = $codigoPartido;

        return $this;
    }

    public function setArbitro(string $arbitro): self
    {
        $this->arbitro = $arbitro;

        return $this;
    }
}
