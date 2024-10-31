<?php
// Vamos garantir que é o WordPress que chama esse arquivo
// e que realmente vai desistalar meu plugin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
  die();
// Vamos remover as opções que criamos na instalação
delete_option( 'Playerzbr_option' );
?>
