<?php

namespace cio;


function make_text_field( array $args ) {

    $defaults = array(
        'type'  => 'text',
        'value' => '',
        'class' => array(),
        'attrs' => array(),
        'desc'  => false
    );

    $args = wp_parse_args( $args, $defaults );

    echo '<input type="' . esc_attr( $args['type'] ) . '" name="' . $args['name'] . '" value="' . esc_attr( $args['value'] ) .
         '" class="' . esc_attr( is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'] ) . '" ';


    if ( ! empty ( $args['attrs'] ) ) {

        foreach ( $args['attrs'] as $attr => $values ) {
            echo $attr . '="' . ( is_array( $values ) ? implode( ' ', $values ) : esc_attr( $values ) ) . '" ';
        }

    }


    echo '/>';

    if ( $args['desc'] ) {
        echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
    }

}

function make_select( array $args ) {

    $defaults = array(
        'selected' => '',
        'class'    => array(),
        'attrs'    => array(),
        'desc'     => false
    );

    $args = wp_parse_args( $args, $defaults );

    echo '<select name="' . $args['name'] . '" class="' .
         esc_attr( is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'] ) . '" ';


    if ( ! empty ( $args['attrs'] ) ) {


        foreach ( $args['attrs'] as $attr => $values ) {
            echo $attr . '="' . ( is_array( $values ) ? implode( ' ', $values ) : esc_attr( $values ) ) . '" ';
        }

    }


    echo '>';

    foreach ( $args['options'] as $value => $title ) {
        echo '<option value="' . esc_attr( $value ) . '" ' . selected( $args['selected'], $value, false ) . '>' .
             esc_html( $title ) . '</option>';
    }

    echo '</select>';

    if ( $args['desc'] ) {
        echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
    }

}

function make_checkbox( array $args ) {

    $defaults = array(
        'checked' => false,
        'class'   => array(),
        'attrs'   => array(),
        'label'   => false,
        'value'   => ''
    );

    $args = wp_parse_args( $args, $defaults );

    echo '<label>';
    echo '<input type="checkbox" ' . checked( $args['checked'], true, false ) . 'name="' . $args['name'] .
         '" class="' . esc_attr( is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'] ) . '" ' .
         ' value="' . $args['value'] . '" ';

    foreach ( $args['attrs'] as $attr => $values ) {
        echo $attr . '="' . esc_attr( is_array( $values ) ? implode( ' ', $args['attrs'] ) : $args['attrs'] ) . '" ';
    }

    echo '/>';

    if ( $args['label'] ) {
        esc_html_e( $args['label'] );
    }

    echo '</label>';

}
