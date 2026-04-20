# Gestión de Prácticas

Este proyecto trata sobre la automatización del proceso
de asignación de prácticas por parte de los tutores. 

Se inicia sesión cómo tutor, y se puede ver/modificar las tablas
de los alumnos y empresas. De igual manera, se pueden hacer 
las asignaciones de las prácticas de uno/varios alumno/s en las
empresas correspondientes. Además, se podrá obtener un volcado
de dichos datos como fichero, para poder usarlos en difentes
documentaciones que corresponden al proceso de prácticas.

De igual manera, existen usuarios administradores que podrán
ver/modificar la tabla tutores, entre otras. Son los que se 
encargan de dar de alta/bajo a los correspondientes tutores 
a los respectivos cursos. No son los mismos tutores los que
se darse de alta/baja como tutores sino los de administracion. 
Ellos si tendrán permisos para ver toda la información.

>Lo anterior corresponde a una versión inicial, beta, de explicación
de la aplicación.

## Flujo de la aplicación

Hay dos formas de entrar a la aplicación, como admin o como tutor. 
En caso de entrar como admin, se verá un panel en el cual se ve una 
tabla con los tutores que existen, sólo eso, por ahora. En caso de 
entrar como tutor, se verá un panel que estará divido en dos: 
La primera parte, como "cabecera", que sirve de bienvenida a la 
aplicación, y contiene un apartado de "perfil" dónde dice tu nombre, 
ciclo, y se puede cerrar sesión, y otro apartado que funciona para
moverme por la aplicación, como una especie de "navegador", aquí es 
dónde está el flujo del sistema, dividido en 4 pasos:

1. Convenios
2. Alumnos
3. Plan Formativo
4. Seguimiento

Estos, como se puede ver en la estructura corresponde a los pasos, 
o "Steps", que se van a ir realizando, sin embargo, no son bloqueantes, 
se puede hacer el paso 1, y luego irse al 3, entrar a la aplicación 
y sólo irse al paso 2, no importa, se puede mover con total libertad. 
La otra parte es dónde se van a ir mostrando los respectivos pasos. 
Cada uno con su funcionalidad correspondiente. 

Lo ideal es seguir el flujo, de los pasos, que es: primero buscar un 
convenio, si no existe agregarlo/crearlo, y si existe ir añadirlo a 
"Mi Listado", parecido a un "favoritos", para usarlo en el paso dos, 
lo otro que se puede hacer es poder aprobar los convenios que se 
"agregaron/crearon" anteriormente, y eso sería por el paso 1; después 
de tener el convenio en favoritos, nos vamos al paso 2, de alumnos, 
en la cual sólo se verán los alumnos del ciclo correspondiente al 
tutor que entra, en este paso se mostrará una tabla con dos partes: 
la inicial para buscar, ordenar, y/o filtrar, y también botones para 
cargar alumnos (funcionalidad para futuras versiones), exportar 
(que por ahora simplemente cambia un boolean dentro de una tabla de 
base de datos, pero que tendrá relevancia más adelante), y agregar un 
alumno, de igual modo, el siguiente apartado es la tabla en sí, divida 
en 3 partes, datos del alumno, datos del convenio (empresa), y datos de 
la colaboración (fechas, horarios, horas, etc), a su vez, tendremos un 
botón para editar al alumno y el resto de datos (que corresponden a la 
tabla asignaciones y convenios), y tres columnas al final: primero 
tenemos a estado (Sin asignar: cuando el alumno no tiene empresa, 
es decir, no está en la tabla asignaciones; en proceso: cuando ya está 
en la tabla pero faltan datos, pues algunos están a NULL; y completado:
cuando tiene relleno todos los datos de la asignación), después a enviado 
(Cuando exporto a los alumnos, es que ya los he enviado, pero no puedo
exportar si hay datos a NULL, deben estar en estado completado), y por 
último firmado (que es para cuando un alumno pasa al paso 3, pero no puede 
firmarse si no está enviado), estas dos últimas columnas son checks; Ahora, 
el paso 3 consiste en ver aquellos alumnos que han sido marcado como firmados, 
no cualquier, igual tiene su buscador, ordenador, y filtro, y la tabla tiene 
4 columnas, una para editar, una para ver el alumno, la empresa que tiene 
asignada ese alumno, y el estado (Exportado o No Exportado), en este caso 
al darle a editar, se nos desplega un nuevo apartado para editar el plan 
formativo, con diversos campos vacíos para rellenar y otros que ya vienen 
cargados de la base de datos, de igual modo abajo están botones que sirven
para volver a la tabla, para volverlo al paso 2 (Que implica que se quiere 
volver a editar la empresa, o la colaboración, y, por ende, deja de estar 
firmado y enviado), y el úlitmo que servirá para exportar (futuras 
funcionalidades); y el último paso, el seguimiento, sigue en desarrollo, 
y, por ahora, se encuentra vacío.