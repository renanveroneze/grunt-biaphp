<?php

    error_reporting(E_ALL^E_NOTICE);


    /*
     *                                 ___           ___         ___           ___
     *      _____                     /\  \         /\  \       /\  \         /\  \
     *     /::\  \       ___         /::\  \       /::\  \      \:\  \       /::\  \
     *    /:/\:\  \     /\__\       /:/\:\  \     /:/\:\__\      \:\  \     /:/\:\__\
     *   /:/ /::\__\   /:/__/      /:/ /::\  \   /:/ /:/  /  ___ /::\  \   /:/ /:/  /
     *  /:/_/:/\:|__| /::\  \     /:/_/:/\:\__\ /:/_/:/  /  /\  /:/\:\__\ /:/_/:/  /
     *  \:\/:/ /:/  / \/\:\  \__  \:\/:/  \/__/ \:\/:/  /   \:\/:/  \/__/ \:\/:/  /
     *   \::/_/:/  /     \:\/\__\  \::/__/       \::/__/     \::/__/       \::/__/
     *    \:\/:/  /       \::/  /   \:\  \        \:\  \      \:\  \        \:\  \
     *     \::/  /        /:/  /     \:\__\        \:\__\      \:\__\        \:\__\
     *      \/__/         \/__/       \/__/         \/__/       \/__/         \/__/
     *
     *
     *  BiaPHP is created by Renan Veroneze and it's licensed under a Creative Commons BY-SA.
     *  © 2014~2015
     *  @0.0.1
     *
     */

    class BiaPHP {

        public $selfies = array();


        /**
         *
         * Read
         *
         * @param  STRING -> $uri -> Path to file
         * @return OBJECT -> $this
         *
         */

        public function read( $uri ) {

            $lines = file( $uri );
            $this->file_lines = $lines;

            return $this;

        }





        /**
         *
         * Parse
         *
         * @param  NULL
         * @return NULL
         *
         */

        public function parse() {

            foreach( $this->file_lines as $line_content ) {

                $tmp[] = $this->quick_rules( $line_content );

            }

            $tmp = $this->close_blocks( $tmp );

            $this->tmp = $tmp;

            return $this;

        }






        /**
         *
         * Write
         *
         * @param  NULL
         * @return NULL
         *
         */

        public function write( $uri ) {

            file_put_contents( $uri, $this->tmp );

        }






        /**
         *
         * Quick Rules
         *
         * @param  STRING -> $line_content
         * @return STRING
         *
         */

        public function quick_rules( $line_content ) {

            $rules = array(
                '/^<\?$/' => '<?php',
                '/(\w+) (static )?(\w+)( \((.*)\))? ->$/' => '$1 $2function $3($5) {',
                '/if (.*)/' => 'if( $1 ) {',
                '/(else)$/' => 'else {',
                '/\$_S\[/' => '$_SESSION[',
                '/\$_P/' => '$_POST',
                '/\$_G/' => '$_GET',
                '/( ~ )/' => ' . ',
                '/(\$\w+)(\.)/' => '$1->',
                '/([^ ]+)(.*)( \?: )(.*)/' => '$1 $2 ? $2 : $4',
                '/(@@)(.*)$/' => 'self::$2',
                '/^(.*[ ]+)(@)(.*)$/' => '$1$this->$3',
                '/(~)(.*)$/' => 'parent::$2',
                '/for (\$\w+)([0-9\[\]]+)? as ((\$\w+) => )?(\$\w+)$/' => 'foreach( $1$2 as $3$5 ) {',
                '/class (\w+)( extends \w+)?$/' => 'class $1$2 {',
                '/^([^{}\n;#]+)$/' => '$1;',
                '/(\)|"|\')$/' => '$1;',
                '/<\?php;/' => '<?php'
            );

            foreach( $rules as $match => $replace ) {

                if(preg_match( $match, $line_content )) {

                    $line_content = preg_replace( $match, $replace, $line_content );

                }

            }


            if(preg_match( '/self class/', $line_content )) {

                preg_match_all( '/self class (\w+)/', $line_content, $selfies );
                preg_match_all( '/([ ]{4})/', $line_content, $matches );
                $ind = count( $matches[0] );
                $tab = implode('', array_fill( 0, $ind, '    ' ));
                $this->selfies[] = "\n" . $tab . 'new ' . $selfies[1][0] . ";\n";

                $line_content = preg_replace( '/self class (.*)/', 'class $1', $line_content );

            }

            return $line_content;

        }






        /**
         *
         * Close Blocks
         *
         * @param
         * @return
         *
         */

        public function close_blocks( $content ) {

            $start_block_pattern = '/([\w ]+)(\((.*)\))? {/';

            foreach( $content as $k => $line ) {

                if( preg_match( $start_block_pattern, $line ) ) {

                    preg_match_all( '/([ ]{4})/', $line, $matches );
                    $ind = count( $matches[0] );
                    $tab = implode('', array_fill( 0, $ind, '    ' ));

                    $close = false;

                    for( $i = $k + 1; $i < count( $content ); $i++ ) {

                        $child_line = $content[$i];

                        preg_match_all( '/([ ]{4})/', $child_line, $child_matches );
                        $child_ind = count( $child_matches[0] );
                        $child_tab = @implode('', array_fill( 0, $child_ind, '    ' ));

                        if( $child_ind != 0 && $child_ind <= $ind) {

                            $ends[] = array( $i, $child_tab );
                            $close = true;
                            break;

                        }

                    }

                    if( !$close ) $others[] = $tab . "}\n";

                }

            }



            $j = -1;

            if( $ends ) {

                foreach( $ends as $v ) {

                    array_splice( $content, $v[0] + $j, 0, $v[1] . "}\n" );
                    $j++;

                }

            }

            foreach( array_reverse( $others ) as $v ) {

                $content[] = $v;

            }


            foreach( $this->selfies as $v ) {

                $content[] = $v;

            }

            $content[] = "\n" . '?>';

            return implode( '', $content );

        }

    }

?>
