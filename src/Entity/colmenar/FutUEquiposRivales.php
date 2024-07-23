<?php

namespace App\Entity\colmenar;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * FutUEquiposRivales
 *
 * @ORM\Table(name="fut_u_equipos_rivales", indexes={@ORM\Index(name="id_detalle_tipo_partido", columns={"id_detalle_tipo_partido"}), @ORM\Index(name="id_equipo", columns={"id_equipo"}), @ORM\Index(name="id_rival", columns={"id_rival"}), @ORM\Index(name="id_estadio", columns={"id_estadio"})})
 * @ORM\Entity()
 */
class FutUEquiposRivales
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_partido", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPartido;
    
    /**
     * @var int|null
     *
     * @ORM\Column(name="codigo_partido", type="integer", nullable=true)
     */
    private $codigoPartido;

    /**
     * @var \FutTEquipos
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\colmenar\FutTEquipos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_equipo", referencedColumnName="id_equipo")
     * })
     */
    private $idEquipo;

    /**
     * @var \FutTRivales
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\colmenar\FutTRivales")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_rival", referencedColumnName="id_rival")
     * })
     */
    private $idRival;

    /**
    * @var FutTEstadios|null
    *
    * @ORM\ManyToOne(targetEntity="App\Entity\colmenar\FutTEstadios")
    * @ORM\JoinColumn(name="id_estadio", referencedColumnName="id_estadio")
    */
    private $idEstadio;

    /**
     * @var FutTDetalleTipoPartido|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\colmenar\FutTDetalleTipoPartido")
     * @ORM\JoinColumn(name="id_detalle_tipo_partido", referencedColumnName="id_detalle_tipo_partido")
     */
    private $idDetalleTipoPartido;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="horario", type="time", nullable=true)
     */
    private $horario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="resultado_local", type="string", length=2, nullable=true)
     */
    private $resultadoLocal = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="resultado_visitante", type="string", length=2, nullable=true)
     */
    private $resultadoVisitante = '';

    /**
     * @var string
     *
     * @ORM\Column(name="local", type="string", length=2, nullable=false)
     */
    private $local = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="observaciones", type="string", length=220, nullable=true)
     */
    private $observaciones;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mostrar", type="integer", nullable=true, options={"default"="1"})
     */
    private $mostrar = 1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="autocar", type="string", length=20, nullable=true)
     */
    private $autocar;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="competicion", type="string", length=60, nullable=true)
     */
    private $competicion;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="grupo", type="string", length=30, nullable=true)
     */
    private $grupo;
    
    /**
     * @ORM\Column(name="jornada",type="string", length=10, nullable=true)
     */
    private $jornada;


    public function getIdPartido(): ?int
    {
        return $this->idPartido;
    }

    public function getIdEstadio(): ?FutTEstadios
    {
        return $this->idEstadio;
    }


    public function setIdEstadio(?FutTEstadios $idEstadio): self
    {
        $this->idEstadio = $idEstadio;

        return $this;
    }

    public function getIdDetalleTipoPartido(): ?FutTDetalleTipoPartido
    {
        return $this->idDetalleTipoPartido;
    }

    public function setIdDetalleTipoPartido(?FutTDetalleTipoPartido $idDetalleTipoPartido): self
    {
        $this->idDetalleTipoPartido = $idDetalleTipoPartido;

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

    public function getHorario(): ?\DateTimeInterface
    {
        return $this->horario;
    }

    public function setHorario(?\DateTimeInterface $horario): self
    {
        $this->horario = $horario;

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

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(string $local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getMostrar(): ?int
    {
        return $this->mostrar;
    }

    public function setMostrar(?int $mostrar): self
    {
        $this->mostrar = $mostrar;

        return $this;
    }

    public function getAutocar(): ?string
    {
        return $this->autocar;
    }

    public function setAutocar(?string $autocar): self
    {
        $this->autocar = $autocar;

        return $this;
    }

    public function getCodigoPartido(): ?int
    {
        return $this->codigoPartido;
    }

    public function setCodigoPartido(?int $codigoPartido): self
    {
        $this->codigoPartido = $codigoPartido;

        return $this;
    }

    public function getIdEquipo(): ?FutTEquipos
    {
        return $this->idEquipo;
    }

    public function setIdEquipo(?FutTEquipos $idEquipo): self
    {
        $this->idEquipo = $idEquipo;

        return $this;
    }

    public function getIdRival(): ?FutTRivales
    {
        return $this->idRival;
    }

    public function setIdRival(?FutTRivales $idRival): self
    {
        $this->idRival = $idRival;

        return $this;
    }
    
    public function getCompeticion(): ?string
    {
        return $this->competicion;
    }

    public function setCompeticion(?string $competicion): self
    {
        $this->competicion = $competicion;

        return $this;
    }
    
    public function getGrupo(): ?string
    {
        return $this->grupo;
    }

    public function setGrupo(?string $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }
    
    public function getJornada(): ?string
    {
        return $this->jornada;
    }
    
    public function setJornada(string $jornada): self
    {
        $this->jornada = $jornada;

        return $this;
    }

}
