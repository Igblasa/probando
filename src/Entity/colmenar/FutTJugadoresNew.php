<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTJugadoresNew
 *
 * @ORM\Table(name="fut_t_jugadores_new", indexes={@ORM\Index(name="id_equipo", columns={"id_equipo"})})
 * @ORM\Entity
 */
class FutTJugadoresNew
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_jugador", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idJugador;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_jug_gesdep", type="string", length=40, nullable=false)
     */
    private $codJugGesdep;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", length=150, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=150, nullable=false)
     */
    private $apellidos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_deportivo", type="string", length=50, nullable=true)
     */
    private $nombreDeportivo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="correo_jugador", type="string", length=100, nullable=true)
     */
    private $correoJugador;

    /**
     * @var string|null
     *
     * @ORM\Column(name="correo_padre", type="string", length=100, nullable=true)
     */
    private $correoPadre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="correo_madre", type="string", length=100, nullable=true)
     */
    private $correoMadre;

    /**
     * @var \FutTEquipos
     *
     * @ORM\ManyToOne(targetEntity="FutTEquipos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_equipo", referencedColumnName="id_equipo")
     * })
     */
    private $idEquipo;

    public function getIdJugador(): ?int
    {
        return $this->idJugador;
    }

    public function getCodJugGesdep(): ?string
    {
        return $this->codJugGesdep;
    }

    public function setCodJugGesdep(string $codJugGesdep): self
    {
        $this->codJugGesdep = $codJugGesdep;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getNombreDeportivo(): ?string
    {
        return $this->nombreDeportivo;
    }

    public function setNombreDeportivo(?string $nombreDeportivo): self
    {
        $this->nombreDeportivo = $nombreDeportivo;

        return $this;
    }

    public function getCorreoJugador(): ?string
    {
        return $this->correoJugador;
    }

    public function setCorreoJugador(?string $correoJugador): self
    {
        $this->correoJugador = $correoJugador;

        return $this;
    }

    public function getCorreoPadre(): ?string
    {
        return $this->correoPadre;
    }

    public function setCorreoPadre(?string $correoPadre): self
    {
        $this->correoPadre = $correoPadre;

        return $this;
    }

    public function getCorreoMadre(): ?string
    {
        return $this->correoMadre;
    }

    public function setCorreoMadre(?string $correoMadre): self
    {
        $this->correoMadre = $correoMadre;

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


}
