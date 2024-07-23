<?php

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DatatablesAjaxPaginacion extends AbstractController
{
    public $parameters;
    
    public function __construct(ParameterBagInterface $parameters) {
        $this->parameters = $parameters;
    }
    
    public function sql($tabla,$columns)
    {
        $sql = "";
        $sql = "SELECT ".implode(', ', $this->pluck($columns, 'db'))." FROM ".$tabla." ";
        return $sql;
    }
    
    public function limit()
    {
        $limit = '';

        if ( isset($_GET['start']) && $_GET['length'] != -1 ) {
            $limit = "LIMIT ".intval($_GET['start']).", ".intval($_GET['length']);
        }
        
        return $limit;
    }
    
    public function order ( $columns )
    {
        $order = '';

        if ( isset($_GET['order']) && count($_GET['order']) ) {
            $orderBy = array();
            $dtColumns = $this->pluck( $columns, 'dt' );

            for ( $i=0, $ien=count($_GET['order']) ; $i<$ien ; $i++ ) {
                $columnIdx = intval($_GET['order'][$i]['column']);
                $requestColumn = $_GET['columns'][$columnIdx];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['orderable'] == 'true' ) {
                    $dir = $_GET['order'][$i]['dir'] === 'asc' ?
                            'ASC' :
                            'DESC';

                    $orderBy[] = '`'.$column['db'].'` '.$dir;
                }
            }
            $order = 'ORDER BY '.implode(', ', $orderBy);
        }

        return $order;
    }
    
    public function filter ($columns)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = $this->pluck( $columns, 'dt' );

        if ( isset($_GET['search']) && $_GET['search']['value'] != '' ) {
            $str = $_GET['search']['value'];

            for ( $i=0, $ien=count($_GET['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $_GET['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['searchable'] == 'true' ) {
                    $globalSearch[] = "`".$column['db']."` LIKE '%$str%'";
                }
            }
        }

        // Individual column filtering
        if ( isset( $_GET['columns'] ) ) {
            for ( $i=0, $ien=count($_GET['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $_GET['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                $str = $requestColumn['search']['value'];

                if ( $requestColumn['searchable'] == 'true' &&
                    $str != '' ) {
                    $globalSearch[] = "`".$column['db']."` LIKE '%$str%'";
                }
            }
        }

        // Combine the filters into a single string
        $where = '';

        if ( count( $globalSearch ) ) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }

        if ( count( $columnSearch ) ) {
            $where = $where === '' ?
            implode(' AND ', $columnSearch) :
            $where .' AND '. implode(' AND ', $columnSearch);
        }

        if ( $where !== '' ) {
            $where = 'WHERE '.$where;
        }

        return $where;
    }
    
    public function pluck ( $a, $prop )
    {
        $out = array();
        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            $out[] = $a[$i][$prop];
        }
        return $out;
    }
}
