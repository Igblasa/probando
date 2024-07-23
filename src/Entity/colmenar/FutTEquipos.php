<?php

namespace App\Entity\colmenar;

use Doctrine\ORM\Mapping as ORM;

/**
 * FutTEquipos
 *
 * @ORM\Table(name="fut_t_equipos")
 * @ORM\Entity
 */
class FutTEquipos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_equipo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEquipo;

    /**
     * @var int
     *
     * @ORM\Column(name="id_rival_bis", type="integer", nullable=false)
     */
    private $idRivalBis;

    /**
     * @var int
     *
     * @ORM\Column(name="id_tipo_equipo", type="integer", nullable=false)
     */
    private $idTipoEquipo;

    /**
     * @var string
     *
     * @ORM\Column(name="equipo", type="string", length=50, nullable=false)
     */
    private $equipo = '';

    /**
     * @var string
     *
     * @ORM\Column(name="equipo_menu", type="string", length=30, nullable=false)
     */
    private $equipoMenu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="imagen", type="string", length=150, nullable=true)
     */
    private $imagen;

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer", nullable=false)
     */
    private $orden;

    /**
     * @var string|null
     *
     * @ORM\Column(name="patrocinio", type="string", length=100, nullable=true)
     */
    private $patrocinio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="web_patrocinio", type="string", length=100, nullable=true)
     */
    private $webPatrocinio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logo_patrocinio", type="string", length=100, nullable=true)
     */
    private $logoPatrocinio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="clasificacion", type="string", length=200, nullable=true)
     */
    private $clasificacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="historico_clasificacion", type="string", length=200, nullable=true)
     */
    private $historicoClasificacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="calendario", type="string", length=200, nullable=true)
     */
    private $calendario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ultima_jornada", type="string", length=200, nullable=true)
     */
    private $ultimaJornada;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goleadores", type="string", length=200, nullable=true)
     */
    private $goleadores;

    /**
     * @var string|null
     *
     * @ORM\Column(name="competicion_actual", type="string", length=50, nullable=true)
     */
    private $competicionActual;

    /**
     * @var string
     *
     * @ORM\Column(name="sexo", type="string", length=50, nullable=false)
     */
    private $sexo;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_equ_gesdep", type="string", length=10, nullable=false)
     */
    private $codEquGesdep;

    public function getIdEquipo(): ?int
    {
        return $this->idEquipo;
    }

    public function getIdRivalBis(): ?int
    {
        return $this->idRivalBis;
    }

    public function setIdRivalBis(int $idRivalBis): self
    {
        $this->idRivalBis = $idRivalBis;

        return $this;
    }

    public function getIdTipoEquipo(): ?int
    {
        return $this->idTipoEquipo;
    }

    public function setIdTipoEquipo(int $idTipoEquipo): self
    {
        $this->idTipoEquipo = $idTipoEquipo;

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

    public function getEquipoMenu(): ?string
    {
        return $this->equipoMenu;
    }

    public function setEquipoMenu(string $equipoMenu): self
    {
        $this->equipoMenu = $equipoMenu;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

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

    public function getPatrocinio(): ?string
    {
        return $this->patrocinio;
    }

    public function setPatrocinio(?string $patrocinio): self
    {
        $this->patrocinio = $patrocinio;

        return $this;
    }

    public function getWebPatrocinio(): ?string
    {
        return $this->webPatrocinio;
    }

    public function setWebPatrocinio(?string $webPatrocinio): self
    {
        $this->webPatrocinio = $webPatrocinio;

        return $this;
    }

    public function getLogoPatrocinio(): ?string
    {
        return $this->logoPatrocinio;
    }

    public function setLogoPatrocinio(?string $logoPatrocinio): self
    {
        $this->logoPatrocinio = $logoPatrocinio;

        return $this;
    }

    public function getClasificacion(): ?string
    {
        return $this->clasificacion;
    }

    public function setClasificacion(?string $clasificacion): self
    {
        $this->clasificacion = $clasificacion;

        return $this;
    }

    public function getHistoricoClasificacion(): ?string
    {
        return $this->historicoClasificacion;
    }

    public function setHistoricoClasificacion(?string $historicoClasificacion): self
    {
        $this->historicoClasificacion = $historicoClasificacion;

        return $this;
    }

    public function getCalendario(): ?string
    {
        return $this->calendario;
    }

    public function setCalendario(?string $calendario): self
    {
        $this->calendario = $calendario;

        return $this;
    }

    public function getUltimaJornada(): ?string
    {
        return $this->ultimaJornada;
    }

    public function setUltimaJornada(?string $ultimaJornada): self
    {
        $this->ultimaJornada = $ultimaJornada;

        return $this;
    }

    public function getGoleadores(): ?string
    {
        return $this->goleadores;
    }

    public function setGoleadores(?string $goleadores): self
    {
        $this->goleadores = $goleadores;

        return $this;
    }

    public function getCompeticionActual(): ?string
    {
        return $this->competicionActual;
    }

    public function setCompeticionActual(?string $competicionActual): self
    {
        $this->competicionActual = $competicionActual;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getCodEquGesdep(): ?string
    {
        return $this->codEquGesdep;
    }

    public function setCodEquGesdep(string $codEquGesdep): self
    {
        $this->codEquGesdep = $codEquGesdep;

        return $this;
    }


}
