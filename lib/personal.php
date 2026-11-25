<?php
// lib/personal.php: personal de la botica (datos de ejemplo inventados para la práctica)
// Se usa un arreglo asociativo multidimensional, como en la clase de arreglos.

function obtenerPersonal() {
    $personal = array(
        array(
            'id'          => 1,
            'foto'        => 'imagenes/personal/rosa-quispe.jpg',
            'nombres'     => 'Rosa Elena',
            'apellidos'   => 'Quispe Mamani',
            'cargo'       => 'Química Farmacéutica - Directora Técnica',
            'area'        => 'Dirección Técnica',
            'turno'       => 'Lunes a sábado, 8:00 a 16:00',
            'correo'      => 'rosa.quispe@inkafarma.com',
            'telefono'    => '987 654 101',
            'ingreso'     => '15/03/2014',
            'experiencia' => '12 años',
            'formacion'   => 'Química Farmacéutica, Universidad Nacional Mayor de San Marcos',
            'descripcion' => 'Responsable técnica de la botica. Garantiza que cada medicamento se dispense de forma segura y conforme a las normas sanitarias.',
            'funciones'   => array(
                'Supervisar la dispensación de medicamentos',
                'Controlar los productos que requieren receta médica',
                'Verificar el cumplimiento de las normas sanitarias',
                'Capacitar al personal técnico'
            )
        ),
        array(
            'id'          => 2,
            'foto'        => 'imagenes/personal/carlos-mendoza.jpg',
            'nombres'     => 'Carlos Alberto',
            'apellidos'   => 'Mendoza Ríos',
            'cargo'       => 'Administrador de Botica',
            'area'        => 'Administración',
            'turno'       => 'Lunes a sábado, 9:00 a 18:00',
            'correo'      => 'carlos.mendoza@inkafarma.com',
            'telefono'    => '987 654 102',
            'ingreso'     => '02/08/2016',
            'experiencia' => '10 años',
            'formacion'   => 'Administración de Empresas, Instituto de Educación Superior Tecnológico',
            'descripcion' => 'Coordina la operación diaria de la sede, el personal y la reposición de productos.',
            'funciones'   => array(
                'Coordinar al personal de la sede',
                'Aprobar pedidos y reposición de stock',
                'Revisar los reportes de inventario',
                'Atender reclamos de los clientes'
            )
        ),
        array(
            'id'          => 3,
            'foto'        => 'imagenes/personal/lucia-paredes.jpg',
            'nombres'     => 'Lucía Fernanda',
            'apellidos'   => 'Paredes Soto',
            'cargo'       => 'Técnica en Farmacia',
            'area'        => 'Dispensación y atención',
            'turno'       => 'Turno mañana, 7:00 a 15:00',
            'correo'      => 'lucia.paredes@inkafarma.com',
            'telefono'    => '987 654 103',
            'ingreso'     => '10/01/2019',
            'experiencia' => '7 años',
            'formacion'   => 'Técnico en Farmacia, Instituto de Educación Superior Tecnológico',
            'descripcion' => 'Atiende a los clientes en mostrador y orienta sobre la presentación y el uso de cada producto.',
            'funciones'   => array(
                'Dispensar medicamentos en mostrador',
                'Verificar fechas de vencimiento',
                'Orientar al cliente sobre la presentación del producto'
            )
        ),
        array(
            'id'          => 4,
            'foto'        => 'imagenes/personal/jorge-huaman.jpg',
            'nombres'     => 'Jorge Luis',
            'apellidos'   => 'Huamán Torres',
            'cargo'       => 'Técnico en Farmacia',
            'area'        => 'Almacén e inventario',
            'turno'       => 'Turno tarde, 13:00 a 21:00',
            'correo'      => 'jorge.huaman@inkafarma.com',
            'telefono'    => '987 654 104',
            'ingreso'     => '21/05/2020',
            'experiencia' => '6 años',
            'formacion'   => 'Técnico en Farmacia, Instituto de Educación Superior Tecnológico',
            'descripcion' => 'Mantiene ordenado el almacén y registra las entradas y salidas de productos.',
            'funciones'   => array(
                'Recibir y ordenar los pedidos',
                'Registrar el stock en el sistema',
                'Avisar los productos con stock crítico'
            )
        ),
        array(
            'id'          => 5,
            'foto'        => 'imagenes/personal/marisol-ccori.jpg',
            'nombres'     => 'Marisol',
            'apellidos'   => 'Ccori Vargas',
            'cargo'       => 'Cajera y Atención al Cliente',
            'area'        => 'Caja',
            'turno'       => 'Turno mañana, 7:00 a 15:00',
            'correo'      => 'marisol.ccori@inkafarma.com',
            'telefono'    => '987 654 105',
            'ingreso'     => '03/02/2021',
            'experiencia' => '5 años',
            'formacion'   => 'Técnico en Administración, Instituto de Educación Superior Tecnológico',
            'descripcion' => 'Realiza el cobro de las ventas y resuelve consultas de precios y promociones.',
            'funciones'   => array(
                'Cobrar y emitir comprobantes',
                'Realizar el cierre de caja',
                'Informar precios y promociones'
            )
        ),
        array(
            'id'          => 6,
            'foto'        => 'imagenes/personal/diego-salazar.jpg',
            'nombres'     => 'Diego Armando',
            'apellidos'   => 'Salazar Rojas',
            'cargo'       => 'Asistente de Ventas',
            'area'        => 'Ventas y orientación',
            'turno'       => 'Turno tarde, 13:00 a 21:00',
            'correo'      => 'diego.salazar@inkafarma.com',
            'telefono'    => '987 654 106',
            'ingreso'     => '14/09/2022',
            'experiencia' => '4 años',
            'formacion'   => 'Técnico en Ventas, Instituto de Educación Superior Tecnológico',
            'descripcion' => 'Apoya la atención en piso de ventas y la exhibición de productos de cuidado personal.',
            'funciones'   => array(
                'Atender consultas en el piso de ventas',
                'Ordenar la exhibición de productos',
                'Apoyar la atención en horas de mayor demanda'
            )
        )
    );
    return $personal;
}
?>
