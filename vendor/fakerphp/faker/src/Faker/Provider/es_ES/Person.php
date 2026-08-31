<?php

namespace Faker\Provider\es_ES;

class Person extends \Faker\Provider\Person
{
    private static $crcMap = ['T', 'R', 'W', 'A', 'G', 'M', 'Y', 'F', 'P', 'D', 'X', 'B', 'N', 'J', 'Z', 'S', 'Q', 'V', 'H', 'L', 'C', 'K', 'E', 'T'];

    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}} {{suffix}}',
        '{{firstNameMale}} {{lastName}} {{suffix}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}} {{suffix}}',
    ];

    /**
     * {@link} http://www.ine.es/daco/daco42/nombyapel/nombyapel.htm
     * {@link} http://www.ine.es/dyngs/INEbase/es/operacion.htm?c=Estadistica_C&cid=1254736177009&menu=ultiDatos&idp=1254734710990
     * Manually added accent marks because the source lacks of them
     */
    protected static $firstNameMale = [
        'Aaron', 'Adam', 'Adrián', 'Aitor', 'Alberto', 'Aleix', 'Alejandro', 'Alex', 'Alonso', 'Álvaro', 'Ander', 'Andrés', 'Ángel', 'Antonio', 'Arnau', 'Asier',
        'Biel', 'Bruno',
        'Carlos', 'César', 'Cristian',
        'Daniel', 'Dario', 'David', 'Diego',
        'Eduardo', 'Enrique', 'Eric', 'Erik',
        'Fernando', 'Francisco', 'Francisco Javier',
        'Gabriel', 'Gael', 'Gerard', 'Gonzalo', 'Guillem', 'Guillermo',
        'Héctor', 'Hugo',
        'Ian', 'Ignacio',
        'Iker', 'Isaac', 'Ismael', 'Iván', 'Izan',
        'Jaime', 'Jan', 'Javier', 'Jesús', 'Joel', 'Jon', 'Jordi', 'Jorge', 'José', 'José Antonio', 'José Manuel', 'Juan', 'Juan José',
        'Leo', 'Lucas', 'Luis',
        'Manuel', 'Marc', 'Marco', 'Marcos', 'Mario', 'Martín', 'Mateo', 'Miguel', 'Miguel Ángel',
        'Nicolás',
        'Oliver', 'Omar', 'Oriol', 'Óscar',
        'Pablo', 'Pedro', 'Pol',
        'Rafael', 'Raúl', 'Rayan', 'Roberto', 'Rodrigo', 'Rubén',
        'Samuel', 'Santiago', 'Saúl', 'Sergio',
        'Unai',
        'Víctor',
        'Yago', 'Yeray',
    ];

    protected static $firstNameFemale = [
        'Abril', 'Adriana', 'África', 'Aina', 'Ainara', 'Ainhoa', 'Aitana', 'Alba', 'Alejandra', 'Alexandra', 'Alexia', 'Alicia', 'Alma', 'Amparo', 'Ana', 'Ana Isabel', 'Ana María', 'Andrea',  'Ángela', 'Ángeles', 'Antonia', 'Ariadna', 'Aurora',
        'Beatriz', 'Berta', 'Blanca',
        'Candela', 'Carla', 'Carlota', 'Carmen', 'Carolina', 'Celia', 'Clara', 'Claudia', 'Cristina',
        'Daniela', 'Diana',
        'Elena', 'Elsa', 'Emilia', 'Encarnación', 'Eva', 'Esther',
        'Fátima', 'Francisca',
        'Gabriela', 'Gloria',
        'Helena',
        'Inés', 'Inmaculada', 'Irene',  'Isabel',
        'Josefa', 'Jimena', 'Juana', 'Julia',
        'Laia', 'Lara', 'Laura', 'Leire', 'Lorena', 'Lidia', 'Lola', 'Lucía', 'Luisa', 'Luna',
        'Malak', 'Manuela', 'Mar', 'Mara', 'Margarita', 'María', 'María Ángeles', 'María Carmen', 'María Dolores', 'María Pilar', 'Marina', 'Marta',  'Martina', 'Mireia', 'Miriam',
        'Nadia', 'Nahia', 'Naia', 'Naiara', 'Natalia', 'Nayara', 'Nerea', 'Nil', 'Noa', 'Noelia', 'Nora', 'Nuria',
        'Olivia', 'Olga', 'Ona',
        'Paola', 'Patricia', 'Pau', 'Paula', 'Pilar',
        'Raquel', 'Rocío', 'Rosa', 'Rosa María', 'Rosario',
        'Salma', 'Sandra', 'Sara', 'Silvia', 'Sofía', 'Sonia',
        'Teresa',
        'Úrsula',
        'Valentina', 'Valeria', 'Vega', 'Vera', 'Verónica', 'Victoria',
        'Yaiza', 'Yolanda',
        'Zoe',
    ];

    protected static $lastName = [
        'Abad', 'Abeyta', 'Abrego', 'Abreu', 'Acevedo', 'Acosta', 'Acuña', 'Adame', 'Adorno', 'Agosto', 'Aguado', 'Aguayo', 'Aguilar', 'Aguilera', 'Aguirre', 'Alanis', 'Alaniz', 'Alarcón', 'Alba', 'Alcala', 'Alcaráz', 'Alcántar', 'Alejandro', 'Alemán', 'Alfaro', 'Alfonso', 'Alicea', 'Almanza', 'Almaráz', 'Almonte', 'Alonso', 'Alonzo', 'Altamirano', 'Alva', 'Alvarado', 'Álvarez', 'Amador', 'Amaya', 'Anaya', 'Andreu', 'Andrés', 'Anguiano', 'Angulo', 'Antón', 'Aparicio', 'Apodaca', 'Aponte', 'Aragón', 'Aranda', 'Araña', 'Arce', 'Archuleta', 'Arellano', 'Arenas', 'Arevalo', 'Arguello', 'Arias', 'Armas', 'Armendáriz', 'Armenta', 'Armijo', 'Arredondo', 'Arreola', 'Arriaga', 'Arribas', 'Arroyo', 'Arteaga', 'Asensio', 'Atencio', 'Ávalos', 'Ávila', 'Avilés', 'Ayala', 'Baca', 'Badillo', 'Baeza', 'Bahena', 'Balderas',
        'Ballesteros', 'Banda', 'Barajas', 'Barela', 'Barragán', 'Barraza', 'Barrera', 'Barreto', 'Barrientos', 'Barrios', 'Barroso', 'Batista', 'Bautista', 'Bañuelos', 'Becerra', 'Beltrán', 'Benavides', 'Benavídez', 'Benito', 'Benítez', 'Bermejo', 'Bermúdez', 'Bernal', 'Berríos', 'Blanco', 'Blasco', 'Blázquez', 'Bonilla', 'Borrego', 'Botello', 'Bravo', 'Briones', 'Briseño', 'Brito', 'Bueno', 'Burgos', 'Bustamante', 'Bustos', 'Báez', 'Betancourt',
        'Caballero', 'Cabello', 'Cabrera', 'Cabán', 'Cadena', 'Caldera', 'Calderón', 'Calero', 'Calvillo', 'Calvo', 'Camacho', 'Camarillo', 'Campos', 'Canales', 'Candelaria', 'Cano', 'Cantú', 'Caraballo', 'Carbajal', 'Carballo', 'Carbonell', 'Cárdenas', 'Cardona', 'Carmona', 'Caro', 'Carranza', 'Carrasco', 'Carrasquillo', 'Carrera', 'Carrero', 'Carretero', 'Carreón', 'Carrillo', 'Carrión', 'Carvajal', 'Casado', 'Casanova', 'Casares', 'Casas', 'Casillas', 'Castañeda', 'Castaño', 'Castellano', 'Castellanos', 'Castillo', 'Castro', 'Casárez', 'Cavazos', 'Cazares', 'Ceballos', 'Cedillo', 'Ceja', 'Centeno', 'Cepeda', 'Cerda', 'Cervantes', 'Cervántez', 'Chacón', 'Chapa', 'Chavarría', 'Chávez', 'Cintrón', 'Cisneros', 'Clemente', 'Cobo', 'Collado', 'Collazo', 'Colunga', 'Colón', 'Concepción', 'Conde', 'Contreras', 'Cordero', 'Cornejo', 'Corona', 'Coronado', 'Corral', 'Corrales', 'Correa', 'Cortés', 'Cortez', 'Cortés', 'Costa', 'Cotto', 'Covarrubias', 'Crespo', 'Cruz', 'Cuellar', 'Cuenca', 'Cuesta', 'Cuevas', 'Curiel', 'Córdoba', 'Córdova',
        'De Anda', 'De Jesús', 'De la Cruz', 'De la Fuente', 'De la Torre', 'Del Río', 'Delacrúz', 'Delafuente', 'Delagarza', 'Delao', 'Delapaz', 'Delarosa', 'Delatorre', 'Deleón', 'Delgadillo', 'Delgado', 'Delrío', 'Delvalle', 'Díez', 'Domenech', 'Domingo', 'Domínguez', 'Domínquez', 'Duarte', 'Dueñas', 'Duran', 'Dávila', 'Díaz',
        'Echevarría', 'Elizondo', 'Enríquez', 'Escalante', 'Escamilla', 'Escobar', 'Escobedo', 'Escribano', 'Escudero', 'Esparza', 'Espinal', 'Espino', 'Espinosa', 'Espinoza', 'Esquibel', 'Esquivel', 'Esteban', 'Esteve', 'Estrada', 'Estévez', 'Expósito',
        'Fajardo', 'Farías', 'Feliciano', 'Fernández', 'Ferrer', 'Fierro', 'Figueroa', 'Flores', 'Flórez', 'Fonseca', 'Font', 'Franco', 'Frías', 'Fuentes',
        'Gaitán', 'Galarza', 'Galindo', 'Gallardo', 'Gallego', 'Gallegos', 'Galván', 'Galán', 'Gamboa', 'Gámez', 'Gaona', 'Garay', 'García', 'Garibay', 'Garica', 'Garrido', 'Garza', 'Gastélum', 'Gaytán', 'Gil', 'Gimeno', 'Giménez', 'Girón', 'Godoy', 'Godínez', 'Gonzáles', 'González', 'Gracia', 'Granado', 'Granados', 'Griego', 'Grijalva', 'Guajardo', 'Guardado', 'Guerra', 'Guerrero', 'Guevara', 'Guillen', 'Gurule', 'Gutiérrez', 'Guzmán', 'Gálvez', 'Gómez',
        'Haro', 'Henríquez', 'Heredia', 'Hernándes', 'Hernando', 'Hernádez', 'Hernández', 'Herrera', 'Herrero', 'Hidalgo', 'Hinojosa', 'Holguín', 'Huerta', 'Hurtado',
        'Ibarra', 'Ibáñez', 'Iglesias', 'Irizarry', 'Izquierdo',
        'Jaime', 'Jaimes', 'Jaramillo', 'Jasso', 'Jiménez', 'Jimínez', 'Juan', 'Jurado', 'Juárez', 'Jáquez',
        'Laboy', 'Lara', 'Laureano', 'Leal', 'Lebrón', 'Ledesma', 'Leiva', 'Lemus', 'Lerma', 'Leyva', 'León', 'Limón', 'Linares', 'Lira', 'Llamas', 'Llorente', 'Loera', 'Lomeli', 'Longoria', 'Lorente', 'Lorenzo', 'Lovato', 'Loya', 'Lozada', 'Lozano', 'Lucas', 'Lucero', 'Lucio', 'Luevano', 'Lugo', 'Luis', 'Luján', 'Luna', 'Luque', 'Lázaro', 'López',
        'Macias', 'Macías', 'Madera', 'Madrid', 'Madrigal', 'Maestas', 'Magaña', 'Malave', 'Maldonado', 'Manzanares', 'Manzano', 'Marco', 'Marcos', 'Mares', 'Marrero', 'Marroquín', 'Martos', 'Martí', 'Martín', 'Martínez', 'Marín', 'Más', 'Mascareñas', 'Mata', 'Mateo', 'Mateos', 'Matos', 'Matías', 'Maya', 'Mayorga', 'Medina', 'Medrano', 'Mejía', 'Melgar', 'Meléndez', 'Mena', 'Menchaca', 'Mendoza', 'Menéndez', 'Meraz', 'Mercado', 'Merino', 'Mesa', 'Meza', 'Miguel', 'Millán', 'Miramontes', 'Miranda', 'Mireles', 'Mojica', 'Molina', 'Mondragón', 'Monroy', 'Montalvo', 'Montañez', 'Montaño', 'Montemayor', 'Montenegro', 'Montero', 'Montes', 'Montez', 'Montoya', 'Mora', 'Moral', 'Morales', 'Morán', 'Moreno', 'Mota', 'Moya', 'Munguía', 'Murillo', 'Muro', 'Muñiz', 'Muñoz', 'Márquez', 'Méndez',
        'Naranjo', 'Narváez', 'Nava', 'Navarrete', 'Navarro', 'Navas', 'Nazario', 'Negrete', 'Negrón', 'Nevárez', 'Nieto', 'Nieves', 'Niño', 'Noriega', 'Nájera', 'Núñez',
        'Ocampo', 'Ocasio', 'Ochoa', 'Ojeda', 'Oliva', 'Olivares', 'Olivas', 'Oliver', 'Olivera', 'Olivo', 'Olivárez', 'Olmos', 'Olvera', 'Ontiveros', 'Oquendo', 'Ordoñez', 'Ordóñez', 'Orellana', 'Ornelas', 'Orosco', 'Orozco', 'Orta', 'Ortega', 'Ortíz', 'Osorio', 'Otero', 'Ozuna',
        'Pabón', 'Pacheco', 'Padilla', 'Padrón', 'Pagan', 'Palacios', 'Palomino', 'Palomo', 'Pantoja', 'Pardo', 'Paredes', 'Parra', 'Partida', 'Pascual', 'Pastor', 'Patiño', 'Paz', 'Pedraza', 'Pedroza', 'Pelayo', 'Peláez', 'Perales', 'Peralta', 'Perea', 'Pereira', 'Peres', 'Peña', 'Pichardo', 'Pineda', 'Pizarro', 'Piña', 'Piñeiro', 'Plaza', 'Polanco', 'Polo', 'Ponce', 'Pons', 'Porras', 'Portillo', 'Posada', 'Pozo', 'Prado', 'Preciado', 'Prieto', 'Puente', 'Puga', 'Puig', 'Pulido', 'Páez', 'Pérez',
        'Quesada', 'Quezada', 'Quintana', 'Quintanilla', 'Quintero', 'Quiroz', 'Quiñones', 'Quiñónez',
        'Rael', 'Ramos', 'Ramírez', 'Ramón', 'Rangel', 'Rascón', 'Raya', 'Razo', 'Redondo', 'Regalado', 'Reina', 'Rendón', 'Rentería', 'Requena', 'Reséndez', 'Rey', 'Reyes', 'Reyna', 'Reynoso', 'Rico', 'Riera', 'Rincón', 'Riojas', 'Rivas', 'Rivera', 'Rivero', 'Robledo', 'Robles', 'Roca', 'Rocha', 'Rodarte', 'Rodrigo', 'Rodríguez', 'Rodríquez', 'Roig', 'Rojas', 'Rojo', 'Roldán', 'Rolón', 'Romero', 'Romo', 'Román', 'Roque', 'Ros', 'Rosa', 'Rosado', 'Rosales', 'Rosario', 'Rosas', 'Roybal', 'Rubio', 'Rueda', 'Ruelas', 'Ruiz', 'Ruvalcaba', 'Ruíz', 'Ríos',
        'Saavedra', 'Saiz', 'Salas', 'Salazar', 'Salcedo', 'Salcido', 'Saldaña', 'Saldivar', 'Salgado', 'Salinas', 'Salvador', 'Samaniego', 'Sanabria', 'Sánchez', 'Sancho', 'Sandoval', 'Santacruz', 'Santamaría', 'Santana', 'Santiago', 'Santillán', 'Santos', 'Sanz', 'Sarabia', 'Sauceda', 'Saucedo', 'Sedillo', 'Segovia', 'Segura', 'Sepúlveda', 'Serna', 'Serra', 'Serrano', 'Serrato', 'Sevilla', 'Sierra', 'Silva', 'Simón', 'Sisneros', 'Sola', 'Solano', 'Soler', 'Soliz', 'Solorio', 'Solorzano', 'Solís', 'Soria', 'Soriano', 'Sosa', 'Sotelo', 'Soto', 'Suárez', 'Sáenz', 'Sáez', 'Sánchez',
        'Tafoya', 'Tamayo', 'Tamez', 'Tapia', 'Tejada', 'Tejeda', 'Tello', 'Terrazas', 'Terán', 'Tijerina', 'Tirado', 'Toledo', 'Toro', 'Torres', 'Tovar', 'Trejo', 'Treviño', 'Trujillo', 'Téllez', 'Tórrez',
        'Ulibarri', 'Ulloa', 'Urbina', 'Ureña', 'Uribe', 'Urrutia', 'Urías',
        'Vaca', 'Valadez', 'Valdez', 'Valdivia', 'Valdés', 'Valencia', 'Valentín', 'Valenzuela', 'Valero', 'Valladares', 'Valle', 'Vallejo', 'Valles', 'Valverde', 'Vanegas', 'Varela', 'Vargas', 'Vega', 'Vela', 'Velasco', 'Velásquez', 'Velázquez', 'Venegas', 'Vera', 'Verdugo', 'Verduzco', 'Vergara', 'Vicente', 'Vidal', 'Viera', 'Vigil', 'Vila', 'Villa', 'Villagómez', 'Villalba', 'Villalobos', 'Villalpando', 'Villanueva', 'Villar', 'Villareal', 'Villarreal', 'Villaseñor', 'Villegas', 'Vásquez', 'Vázquez', 'Vélez', 'Véliz',
        'Ybarra', 'Yáñez',
        'Zambrano', 'Zamora', 'Zamudio', 'Zapata', 'Zaragoza', 'Zarate', 'Zavala', 'Zayas', 'Zelaya', 'Zepeda', 'Zúñiga',
    ];

    protected static $titleMale = ['Sr.', 'D.', 'Dr.', 'Lic.', 'Ing.'];

    protected static $titleFemale = ['Sra.', 'Srta.', 'Dña', 'Dr.', 'Lic.', 'Ing.'];

    private static $suffix = ['Hijo', 'Segundo', 'Tercero'];

    protected static $licenceCodes = ['AM', 'A1', 'A2', 'A', 'B', 'B+E', 'C1', 'C1+E', 'C', 'C+E', 'D1', 'D1+E', 'D', 'D+E'];

    /**
     * @example 'Hijo'
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }

    /**
     * Generate a Documento Nacional de Identidad (DNI) number
     *
     * @example '77446565E'
     *
     * @see https://es.wikibooks.org/wiki/Algoritmo_para_obtener_la_letra_del_NIF#Algoritmo
     */
    public static function dni()
    {
        $number = static::numerify('########');
        $letter = self::$crcMap[$number % 23];

        return $number . $letter;
    }

    /**
     * @see https://sede.dgt.gob.es/es/tramites-y-multas/permiso-de-conduccion/obtencion-permiso-licencia-conduccion/clases-permiso-conduccion-edad.shtml
     *
     * @return string
     */
    public function licenceCode()
    {
        return static::randomElement(static::$licenceCodes);
    }
}
