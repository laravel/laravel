<?php

namespace Faker\Provider\es_VE;

class Person extends \Faker\Provider\Person
{
    /**
     * CNE is the official national election registry org.
     *
     * @see http://www.cne.gob.ve/web/registro_electoral/ciudadanos_111_129_2011.php
     */
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}} {{suffix}}',
        '{{firstNameMale}} {{lastName}} {{suffix}}',
    ];

    /**
     * CNE is the official national election registry org.
     *
     * @see http://www.cne.gob.ve/web/registro_electoral/ciudadanos_111_129_2011.php
     */
    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}} {{suffix}}',
    ];

    /**
     * CNE is the official national election registry org.
     *
     * @see http://www.cne.gob.ve/web/registro_electoral/ciudadanos_111_129_2011.php
     */
    protected static $firstNameMale = [
        'Aaron', 'Adam', 'Adria', 'Adrian', 'Alberto', 'Aleix', 'Alejandro', 'Alex', 'Alonso', 'Alvaro', 'Ander', 'Andres',
        'Angel', 'Antonio', 'Bruno', 'Carlos', 'Cesar', 'Cristian', 'Daniel', 'Dario', 'David', 'Domingo',
        'Diego', 'Eduardo', 'Enrique', 'Eric', 'Erik', 'Fernando', 'Francisco', 'Francisco Javier', 'Gabriel', 'Gonzalo',
        'Guillem', 'Guillermo', 'Hector', 'Hugo', 'Ian', 'Ignacio', 'Isaac', 'Ismael', 'Ivan', 'Izan', 'Jaime',
        'Jan', 'Javier', 'Jesus', 'Joel', 'Jon', 'Jordi', 'Jorge', 'Jose', 'Juan', 'Leonardo', 'Leandro',
        'Leo', 'Lucas', 'Luis', 'Manuel', 'Marc', 'Marco', 'Marcos', 'Mario', 'Martin', 'Mateo', 'Miguel', 'Miguel',
        'Mohamed', 'Nicolas', 'Oliver', 'Omar', 'Oswaldo', 'Oscar', 'Pablo', 'Pedro', 'Pol', 'Rafael', 'Raul', 'Rayan',
        'Roberto', 'Rodrigo', 'Ruben', 'Samuel', 'Santiago', 'Saul', 'Sergio', 'Sebastian', 'Victor', 'Yorman',
    ];

    /**
     * CNE is the official national election registry org.
     *
     * @see http://www.cne.gob.ve/web/registro_electoral/ciudadanos_111_129_2011.php
     */
    protected static $firstNameFemale = [
        'Abril', 'Adriana', 'Africa', 'Ainara', 'Antonia', 'Alba', 'Alejandra', 'Alexandra', 'Alexia', 'Alicia', 'Alma',
        'Ana', 'Andrea', 'Ane', 'Angela', 'Anna', 'Ariadna', 'Aroa', 'Bella', 'Beatriz', 'Berta', 'Blanca', 'Candela',
        'Carla', 'Carlota', 'Carmen', 'Carolina', 'Celia', 'Clara', 'Claudia', 'Cristina', 'Daniela', 'Diana', 'Elena', 'Elsa',
        'Emma', 'Erika', 'Eva', 'Fatima', 'Gabriela', 'Helena', 'Ines', 'Irene', 'Iria', 'Isabel', 'Jana', 'Jimena',
        'Joan', 'Julia', 'Laia', 'Lara', 'Laura', 'Leire', 'Leyre', 'Lidia', 'Lola', 'Lucia', 'Luna', 'Luisa',
        'Manuela', 'Mar', 'Mara', 'Maria', 'Marina', 'Marta', 'Marti', 'Martina', 'Mireia', 'Miriam', 'Nadia', 'Nahia',
        'Naia', 'Naiara', 'Natalia', 'Nayara', 'Nerea', 'Nil', 'Noa', 'Noelia', 'Nora', 'Nuria', 'Olivia', 'Ona',
        'Paola', 'Patricia', 'Pau', 'Paula', 'Raquel', 'Rocio', 'Salma', 'Sandra', 'Sara', 'Silvia', 'Sofia', 'Teresa',
        'Valentina', 'Valeria', 'Vega', 'Vera', 'Victoria', 'Yaiza', 'Zulay',
    ];

    /**
     * CNE is the official national election registry org.
     *
     * @see http://www.cne.gob.ve/web/registro_electoral/ciudadanos_111_129_2011.php
     */
    protected static $lastName = [
        'Abad', 'Abeyta', 'Abrego', 'Abreu', 'Acevedo', 'Acosta', 'Acuña', 'Adame', 'Adorno', 'Agosto', 'Aguado', 'Aguayo',
        'Aguilar', 'Aguilera', 'Aguirre', 'Alanis', 'Alaniz', 'Alarcón', 'Alba', 'Alcala', 'Alcaraz', 'Alcántar', 'Alejandro',
        'Alemán', 'Alfaro', 'Alfonso', 'Alicea', 'Almanza', 'Almaraz', 'Almonte', 'Alonso', 'Alonzo', 'Altamirano', 'Alva',
        'Alvarado', 'Amador', 'Amaya', 'Anaya', 'Andreu', 'Andrés', 'Anguiano', 'Angulo', 'Antón', 'Aparicio', 'Apodaca',
        'Aponte', 'Aragón', 'Aranda', 'Araña', 'Arce', 'Archuleta', 'Arellano', 'Arenas', 'Arevalo', 'Arguello', 'Arias',
        'Armas', 'Armendáriz', 'Armenta', 'Armijo', 'Arredondo', 'Arreola', 'Arriaga', 'Arribas', 'Arroyo', 'Arteaga', 'Asensio',
        'Atencio', 'Avilés', 'Ayala', 'Baca', 'Badillo', 'Baeza', 'Bahena', 'Balderas', 'Ballesteros', 'Banda', 'Barajas', 'Barela',
        'Barragán', 'Barraza', 'Barrera', 'Barreto', 'Barrientos', 'Barrios', 'Barroso', 'Batista', 'Bautista', 'Bañuelos', 'Becerra',
        'Beltrán', 'Benavides', 'Benavídez', 'Benito', 'Benítez', 'Bermejo', 'Bermúdez', 'Bernal', 'Berríos', 'Blanco', 'Blasco',
        'Blázquez', 'Bonilla', 'Borrego', 'Botello', 'Bravo', 'Briones', 'Briseño', 'Brito', 'Bueno', 'Burgos', 'Bustamante',
        'Bustos', 'Báez', 'Bétancourt', 'Caballero', 'Cabello', 'Cabrera', 'Cabán', 'Cadena', 'Caldera', 'Calderón', 'Calero',
        'Calvillo', 'Calvo', 'Camacho', 'Camarillo', 'Campos', 'Canales', 'Candelaria', 'Cano', 'Cantú', 'Caraballo', 'Carbajal',
        'Carballo', 'Carbonell', 'Cardenas', 'Cardona', 'Carmona', 'Caro', 'Carranza', 'Carrasco', 'Carrasquillo', 'Carrera',
        'Carrero', 'Carretero', 'Carreón', 'Carrillo', 'Carrion', 'Carrión', 'Carvajal', 'Casado', 'Casanova', 'Casares', 'Casas',
        'Casillas', 'Castañeda', 'Castaño', 'Castellano', 'Castellanos', 'Castillo', 'Castro', 'Casárez', 'Cavazos', 'Cazares',
        'Ceballos', 'Cedillo', 'Ceja', 'Centeno', 'Cepeda', 'Cerda', 'Cervantes', 'Cervántez', 'Chacón', 'Chapa', 'Chavarría',
        'Chávez', 'Cintrón', 'Cisneros', 'Clemente', 'Cobo', 'Collado', 'Collazo', 'Colunga', 'Colón', 'Concepción', 'Conde',
        'Contreras', 'Cordero', 'Cornejo', 'Corona', 'Coronado', 'Corral', 'Corrales', 'Correa', 'Cortes', 'Cortez', 'Cortés',
        'Costa', 'Cotto', 'Covarrubias', 'Crespo', 'Cruz', 'Cuellar', 'Cuenca', 'Cuesta', 'Cuevas', 'Curiel', 'Córdoba', 'Córdova',
        'De la cruz', 'De la fuente', 'De la torre', 'Del río', 'Delacrúz', 'Delafuente', 'Delagarza', 'Delao', 'Delapaz', 'Delarosa',
        'Delatorre', 'Deleón', 'Delgadillo', 'Delgado', 'Delrío', 'Delvalle', 'Diez', 'Domenech', 'Domingo', 'Domínguez', 'Domínquez',
        'Duarte', 'Dueñas', 'Duran', 'Dávila', 'Díaz', 'Echevarría', 'Elizondo', 'Enríquez', 'Escalante', 'Escamilla', 'Escobar',
        'Escobedo', 'Escribano', 'Escudero', 'Esparza', 'Espinal', 'Espino', 'Espinosa', 'Espinoza', 'Esquibel', 'Esquivel', 'Esteban',
        'Esteve', 'Estrada', 'Estévez', 'Expósito', 'Fajardo', 'Farías', 'Feliciano', 'Fernández', 'Ferrer', 'Fierro', 'Figueroa',
        'Flores', 'Flórez', 'Fonseca', 'Font', 'Franco', 'Frías', 'Fuentes', 'Gaitán', 'Galarza', 'Galindo', 'Gallardo', 'Gallego',
        'Gallegos', 'Galván', 'Galán', 'Gamboa', 'Gamez', 'Gaona', 'Garay', 'García', 'Garibay', 'Garica', 'Garrido', 'Garza', 'Gastélum',
        'Gaytán', 'Gil', 'Gimeno', 'Giménez', 'Girón', 'Godoy', 'Godínez', 'Gonzales', 'González', 'Gracia', 'Granado', 'Granados',
        'Griego', 'Grijalva', 'Guajardo', 'Guardado', 'Guerra', 'Guerrero', 'Guevara', 'Guillen', 'Gurule', 'Gutiérrez', 'Guzmán',
        'Gálvez', 'Gómez', 'Haro', 'Henríquez', 'Heredia', 'Hernandes', 'Hernando', 'Hernádez', 'Hernández', 'Herrera', 'Herrero',
        'Hidalgo', 'Hinojosa', 'Holguín', 'Huerta', 'Hurtado', 'Ibarra', 'Ibáñez', 'Iglesias', 'Irizarry', 'Izquierdo', 'Jaime', 'Jaimes',
        'Jaramillo', 'Jasso', 'Jiménez', 'Jimínez', 'Juan', 'Jurado', 'Juárez', 'Jáquez', 'Laboy', 'Lara', 'Laureano', 'Leal', 'Lebrón',
        'Ledesma', 'Leiva', 'Lemus', 'Lerma', 'Leyva', 'León', 'Limón', 'Linares', 'Lira', 'Llamas', 'Llorente', 'Loera', 'Lomeli',
        'Longoria', 'Lorente', 'Lorenzo', 'Lovato', 'Loya', 'Lozada', 'Lozano', 'Lucas', 'Lucero', 'Lucio', 'Luevano', 'Lugo', 'Luis',
        'Luján', 'Luna', 'Luque', 'Lázaro', 'López', 'Macias', 'Macías', 'Madera', 'Madrid', 'Madrigal', 'Maestas', 'Magaña', 'Malave',
        'Maldonado', 'Manzanares', 'Manzano', 'Marco', 'Marcos', 'Mares', 'Marrero', 'Marroquín', 'Martos', 'Martí', 'Martín', 'Martínez',
        'Marín', 'Mas', 'Mascareñas', 'Mata', 'Mateo', 'Mateos', 'Matos', 'Matías', 'Maya', 'Mayorga', 'Medina', 'Medrano', 'Mejía',
        'Melgar', 'Meléndez', 'Mena', 'Menchaca', 'Mendoza', 'Menéndez', 'Meraz', 'Mercado', 'Merino', 'Mesa', 'Meza', 'Miguel',
        'Millán', 'Miramontes', 'Miranda', 'Mireles', 'Mojica', 'Molina', 'Mondragón', 'Monroy', 'Montalvo', 'Montañez', 'Montaño',
        'Montemayor', 'Montenegro', 'Montero', 'Montes', 'Montez', 'Montoya', 'Mora', 'Moral', 'Morales', 'Moran', 'Moreno', 'Mota',
        'Moya', 'Munguía', 'Murillo', 'Muro', 'Muñiz', 'Muñoz', 'Muñóz', 'Márquez', 'Méndez', 'Naranjo', 'Narváez', 'Nava', 'Navarrete',
        'Navarro', 'Navas', 'Nazario', 'Negrete', 'Negrón', 'Nevárez', 'Nieto', 'Nieves', 'Niño', 'Noriega', 'Nájera', 'Núñez', 'Ocampo',
        'Ocasio', 'Ochoa', 'Ojeda', 'Oliva', 'Olivares', 'Olivas', 'Oliver', 'Olivera', 'Olivo', 'Olivárez', 'Olmos', 'Olvera', 'Ontiveros',
        'Oquendo', 'Ordoñez', 'Ordóñez', 'Orellana', 'Ornelas', 'Orosco', 'Orozco', 'Orta', 'Ortega', 'Ortiz', 'Ortíz', 'Osorio', 'Otero',
        'Ozuna', 'Oropeza', 'Pabón', 'Pacheco', 'Padilla', 'Padrón', 'Pagan', 'Palacios', 'Palomino', 'Palomo', 'Pantoja', 'Pardo', 'Paredes',
        'Parra', 'Partida', 'Pascual', 'Pastor', 'Patiño', 'Paz', 'Pedraza', 'Pedroza', 'Pelayo', 'Peláez', 'Perales', 'Peralta',
        'Perea', 'Pereira', 'Peres', 'Peña', 'Pichardo', 'Pineda', 'Pizarro', 'Piña', 'Piñeiro', 'Plaza', 'Polanco', 'Polo', 'Ponce',
        'Pons', 'Porras', 'Portillo', 'Posada', 'Pozo', 'Prado', 'Preciado', 'Prieto', 'Puente', 'Puga', 'Puig', 'Pulido', 'Páez',
        'Pérez', 'Quesada', 'Quezada', 'Quintana', 'Quintanilla', 'Quintero', 'Quiroz', 'Quiñones', 'Quiñónez', 'Rael', 'Ramos', 'Ramírez',
        'Ramón', 'Rangel', 'Rascón', 'Raya', 'Razo', 'Redondo', 'Regalado', 'Reina', 'Rendón', 'Rentería', 'Requena', 'Reséndez', 'Rey',
        'Reyes', 'Reyna', 'Reynoso', 'Rico', 'Riera', 'Rincón', 'Riojas', 'Rivas', 'Rivera', 'Rivero', 'Robledo', 'Robles', 'Roca', 'Rocha',
        'Rodarte', 'Rodrigo', 'Rodrígez', 'Rodríguez', 'Rodríquez', 'Roig', 'Rojas', 'Rojo', 'Roldan', 'Roldán', 'Rolón', 'Romero', 'Romo',
        'Román', 'Roque', 'Ros', 'Rosa', 'Rosado', 'Rosales', 'Rosario', 'Rosas', 'Roybal', 'Rubio', 'Rueda', 'Ruelas', 'Ruiz', 'Ruvalcaba',
        'Ruíz', 'Ríos', 'Saavedra', 'Saiz', 'Salas', 'Salazar', 'Salcedo', 'Salcido', 'Saldaña', 'Saldivar', 'Salgado', 'Salinas', 'Salvador',
        'Samaniego', 'Sanabria', 'Sanches', 'Sancho', 'Sandoval', 'Santacruz', 'Santamaría', 'Santana', 'Santiago', 'Santillán', 'Santos',
        'Sanz', 'Sarabia', 'Sauceda', 'Saucedo', 'Sedillo', 'Segovia', 'Segura', 'Sepúlveda', 'Serna', 'Serra', 'Serrano', 'Serrato', 'Sevilla',
        'Sierra', 'Silva', 'Simón', 'Sisneros', 'Sola', 'Solano', 'Soler', 'Soliz', 'Solorio', 'Solorzano', 'Solís', 'Soria', 'Soriano',
        'Sosa', 'Sotelo', 'Soto', 'Suárez', 'Sáenz', 'Sáez', 'Sánchez', 'Tafoya', 'Tamayo', 'Tamez', 'Tapia', 'Tejada', 'Tejeda', 'Tello',
        'Terrazas', 'Terán', 'Tijerina', 'Tirado', 'Toledo', 'Tomas', 'Toro', 'Torres', 'Tovar', 'Trejo', 'Treviño', 'Trujillo', 'Téllez',
        'Tórrez', 'Ulibarri', 'Ulloa', 'Urbina', 'Ureña', 'Uribe', 'Urrutia', 'Urías', 'Vaca', 'Valadez', 'Valdez', 'Valdivia', 'Valdés',
        'Valencia', 'Valentín', 'Valenzuela', 'Valero', 'Valladares', 'Valle', 'Vallejo', 'Valles', 'Valverde', 'Vanegas', 'Varela', 'Vargas',
        'Vega', 'Vela', 'Velasco', 'Velásquez', 'Velázquez', 'Venegas', 'Vera', 'Verdugo', 'Verduzco', 'Vergara', 'Vicente', 'Vidal', 'Viera',
        'Vigil', 'Vila', 'Villa', 'Villagómez', 'Villalba', 'Villalobos', 'Villalpando', 'Villanueva', 'Villar', 'Villareal', 'Villarreal',
        'Villaseñor', 'Villegas', 'Vásquez', 'Vázquez', 'Vélez', 'Véliz', 'Ybarra', 'Yáñez', 'Zambrano', 'Zamora', 'Zamudio', 'Zapata',
        'Zaragoza', 'Zarate', 'Zavala', 'Zayas', 'Zelaya', 'Zepeda', 'Zúñiga', 'de Anda', 'de Jesús', 'Águilar', 'Álvarez', 'Ávalos', 'Ávila',
    ];

    protected static $titleMale = ['Sr.', 'Dn.', 'Dr.', 'Lcdo.', 'Ing.'];

    protected static $titleFemale = ['Sra.', 'Srita.', 'Dra.', 'Lcda.', 'Ing.'];

    private static $suffix = ['Hijo'];

    private static $nationalityId = ['V', 'E'];

    /**
     * @example 'Hijo'
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }

    /**
     * Generate random national identification number (cédula de identidad). Ex V-8756432
     *
     * @param string $separator
     *
     * @return string CNE is the official national election registry org.
     *                CNE is the official national election registry org.
     *
     * @see http://www.cne.gob.ve/web/registro_electoral/ciudadanos_111_129_2011.php
     */
    public function nationalId($separator = '')
    {
        $id = static::randomElement(static::$nationalityId);

        if ($id == 'V') {
            return $id . $separator . $this->numberBetween(10000, 100000000);
        }

        return $id . $separator . $this->numberBetween(80000000, 100000000);
    }
}
