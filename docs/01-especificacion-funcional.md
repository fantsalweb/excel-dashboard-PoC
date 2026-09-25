# 1. Objetivo y alcance

## 1.1 Objetivo

Excel Dashboard tiene como objetivo transformar información contenida en archivos Excel en un conjunto de datos estructurado, interpretable y reutilizable dentro de una aplicación web.

El sistema permitirá al usuario cargar un archivo Excel, seleccionar las hojas que desea analizar y definir qué parte de cada hoja contiene la información que debe tratarse como una tabla de datos.

A partir de esa definición, la aplicación analizará las columnas existentes, propondrá un tipo de dato para cada una y permitirá al usuario modificarlo cuando la detección automática no sea adecuada.

El usuario podrá aplicar transformaciones básicas sobre los valores de las columnas antes de que estos sean incorporados al conjunto de datos definitivo. El sistema conservará el valor original y permitirá comprobar el resultado de las transformaciones realizadas.

Una vez completado este proceso, Excel Dashboard generará un **Dataset interno normalizado**, que será la base para las funcionalidades posteriores de visualización, filtrado, generación de gráficos e informes.

El objetivo fundamental es separar el formato y las particularidades del archivo Excel original de las funcionalidades posteriores de la aplicación. Una vez generado el Dataset, estas funcionalidades no deberían depender de cómo estaba estructurado originalmente el archivo.

## 1.2 Alcance de la primera versión

La primera versión del sistema estará orientada al tratamiento de archivos Excel en formato `.xlsx`.

El flujo funcional inicial comprenderá:

1. Carga de un archivo Excel.
2. Lectura del libro y obtención de sus hojas.
3. Selección de las hojas que se desean procesar.
4. Definición de la zona de datos de cada hoja.
5. Previsualización de los datos seleccionados.
6. Identificación de las columnas existentes.
7. Detección automática del tipo de dato de cada columna.
8. Posibilidad de modificar manualmente el tipo detectado.
9. Aplicación de transformaciones básicas sobre los valores.
10. Validación de los datos resultantes.
11. Generación de un Dataset interno normalizado.
12. Utilización posterior de dicho Dataset como fuente para las funcionalidades de análisis y visualización.

## 1.3 Tipos de datos iniciales

La primera versión utilizará un conjunto reducido de tipos de datos generales:

* **Texto**
* **Número**
* **Fecha**
* **Booleano**

La detección automática tendrá en cuenta el contenido de la columna en su conjunto y no únicamente el valor de una celda.

El sistema deberá contemplar situaciones habituales en archivos Excel reales, como celdas vacías, valores con formatos diferentes, fórmulas, valores almacenados como texto y valores que requieren una transformación previa para poder ser interpretados correctamente.

No se pretende que la primera versión identifique automáticamente conceptos semánticos complejos como porcentajes, cantidades con unidades, rangos, umbrales o indicadores específicos. Estos casos podrán tratarse inicialmente mediante transformaciones configuradas por el usuario y podrán dar lugar a funcionalidades más avanzadas en versiones posteriores.

## 1.4 Principios funcionales

El diseño de Excel Dashboard seguirá los siguientes principios:

### Control del usuario

La aplicación podrá realizar propuestas automáticas, pero el usuario tendrá la posibilidad de revisar y modificar las decisiones tomadas por el sistema.

### Transparencia

El sistema deberá permitir conocer cómo se ha interpretado y transformado la información antes de incorporarla al Dataset.

### Conservación de los datos originales

Las transformaciones no deberán destruir el valor original procedente del Excel. El sistema deberá mantener la posibilidad de comparar el valor original con el valor resultante.

### No realizar conversiones silenciosas

Cuando un valor no pueda convertirse de forma válida al tipo seleccionado, el sistema deberá identificarlo como un problema de validación en lugar de modificarlo o descartarlo silenciosamente.

### Separación entre origen y Dataset

El archivo Excel será considerado una fuente de datos, no el modelo interno de la aplicación.

El Dataset generado constituirá un contrato interno estable que permitirá desarrollar posteriormente diferentes funcionalidades independientemente del formato concreto del archivo de origen.

## 1.5 Fuera del alcance inicial

Las siguientes funcionalidades no formarán parte de la primera fase del desarrollo del núcleo de importación y normalización:

* Soporte para múltiples tablas independientes dentro de una misma hoja.
* Procesamiento automático de estructuras Excel especialmente complejas.
* Conexión con Google Drive.
* Importación desde CSV u otras fuentes.
* Persistencia definitiva de los Datasets en una base de datos.
* Sistema de usuarios y autenticación.
* Multitenencia.
* Sistema de suscripciones o pagos.
* Automatizaciones externas.
* Detección semántica avanzada de indicadores.
* Inteligencia artificial para interpretar los datos.

Estas funcionalidades podrán incorporarse posteriormente sin modificar el concepto fundamental del sistema, utilizando el Dataset como punto de separación entre la entrada de datos y las funcionalidades de análisis.

## 1.6 Resultado esperado

Al finalizar esta fase, el sistema deberá ser capaz de realizar el siguiente proceso:

**Excel → extracción → definición de tabla → detección de tipos → configuración del usuario → transformaciones → validación → Dataset normalizado**

El resultado de este proceso será un Dataset que pueda utilizarse posteriormente para construir tablas, filtros, gráficos, informes y otras funcionalidades de análisis sin necesidad de volver a interpretar directamente el archivo Excel original.

# 2. Flujo completo de usuario

## 2.1 Visión general

El flujo de usuario de Excel Dashboard estará dividido en varias fases consecutivas. Cada fase tendrá una responsabilidad concreta y permitirá al usuario revisar el resultado antes de continuar.

El flujo completo será:

**Cargar Excel → Seleccionar hoja → Definir tabla → Previsualizar datos → Configurar columnas → Detectar tipos → Aplicar transformaciones → Validar datos → Generar Dataset → Analizar y visualizar**

De esta forma, el sistema no pasará directamente del archivo Excel a un gráfico o informe, sino que existirá un proceso intermedio de interpretación y preparación de los datos.

---

## 2.2 Fase 1 — Carga del archivo

El usuario accederá a Excel Dashboard y seleccionará un archivo Excel en formato `.xlsx`.

La aplicación comprobará que el archivo recibido es válido y podrá almacenarlo temporalmente para su procesamiento.

Una vez cargado correctamente, el sistema leerá el libro Excel y obtendrá información básica sobre su estructura:

* Nombre del archivo.
* Número de hojas.
* Nombre de cada hoja.
* Dimensiones aproximadas de cada hoja.
* Información necesaria para permitir su posterior configuración.

El usuario no tendrá que configurar inicialmente el contenido de las hojas.

---

## 2.3 Fase 2 — Selección de hoja

Después de cargar el archivo, la aplicación mostrará las hojas disponibles.

Ejemplo:

```text
CONTROL DE PESO Y TENSIÓN.xlsx

Hojas disponibles:

┌──────────────────────────────┐
│ Histórico              [Configurar] │
│ Objetivos               [Configurar] │
│ Alimentación y ejercicio [Configurar] │
│ Ejercicios fuerza       [Configurar] │
│ Hoja 9                  [Configurar] │
└──────────────────────────────┘
```

El usuario podrá decidir qué hojas desea procesar.

Una hoja podrá:

* Configurarse.
* Ignorarse.
* Procesarse posteriormente.

En la primera versión se trabajará con **una única tabla de datos por hoja**.

La aplicación no asumirá que todas las hojas de un libro contienen datos estructurados de la misma manera. Una hoja puede contener una tabla, otra una plantilla y otra información que el usuario no desea procesar.

---

## 2.4 Fase 3 — Definición de la tabla

Una vez seleccionada una hoja, el usuario deberá indicar qué parte de la hoja representa la tabla que desea procesar.

La definición permitirá establecer, como mínimo:

* Fila donde comienzan los encabezados.
* Columna inicial.
* Columna final.
* Fila donde comienzan los datos.
* Fila final de los datos.

La aplicación podrá realizar una propuesta automática basándose en la estructura detectada, pero el usuario podrá corregirla.

Por ejemplo:

```text
Hoja: Histórico

Fila de encabezados: 8
Columna inicial: A
Columna final: V
Primera fila de datos: 10
Última fila de datos: 1187

                 [Continuar]
```

Esta fase es necesaria porque un Excel real puede contener títulos, leyendas, filas vacías, fórmulas, información auxiliar u otros elementos antes o después de la tabla.

---

## 2.5 Fase 4 — Previsualización

Antes de comenzar la interpretación de los datos, el sistema mostrará una previsualización de la tabla definida.

La previsualización permitirá al usuario comprobar visualmente que ha seleccionado correctamente la zona de datos.

Se mostrarán:

* Encabezados.
* Primeras filas de datos.
* Valores de las columnas.
* Posibles celdas vacías.
* Información relevante sobre la estructura detectada.

El usuario podrá volver a la definición de la tabla si observa que la selección no es correcta.

La aplicación no deberá continuar con la configuración de tipos hasta que la zona de datos haya sido definida.

---

## 2.6 Fase 5 — Configuración de columnas

Una vez confirmada la tabla, el sistema identificará sus columnas.

Para cada columna se mostrará información básica y la configuración disponible.

Ejemplo:

```text
PESO (KG)

Tipo detectado:
Número

Tipo utilizado:
[ Número ▼ ]

Valores de ejemplo:
105.17
103.10
99.45
-

Transformaciones:
[ + Añadir transformación ]
```

El usuario podrá revisar cada columna individualmente.

Para cada columna se deberá poder consultar, al menos:

* Nombre de la columna.
* Tipo detectado.
* Tipo seleccionado actualmente.
* Algunos valores de ejemplo.
* Información básica sobre valores vacíos o problemáticos.
* Transformaciones configuradas.

---

## 2.7 Fase 6 — Detección de tipos

La aplicación analizará los valores de cada columna para proponer un tipo de dato.

La detección se realizará sobre el conjunto de valores de la columna y no sobre una única celda.

Los tipos iniciales serán:

* Texto.
* Número.
* Fecha.
* Booleano.

La aplicación deberá tener en cuenta que una misma columna puede contener valores diferentes.

Por ejemplo:

```text
PESO (KG)

105.17
103.10
99.45
-
101.20
```

Aunque exista un valor `-`, la mayoría de los valores pueden indicar que la columna representa números.

La aplicación deberá distinguir entre:

* Valores válidos.
* Valores vacíos.
* Valores que no coinciden con el tipo propuesto.
* Valores que podrían necesitar una transformación.

La detección automática será una propuesta, no una decisión irreversible.

---

## 2.8 Fase 7 — Configuración manual del tipo

El usuario podrá modificar el tipo propuesto por el sistema.

Por ejemplo:

```text
Tipo detectado: Número

Tipo utilizado:
[ Número ▼ ]

Opciones:
- Texto
- Número
- Fecha
- Booleano
```

Esta posibilidad será importante porque la interpretación de un dato no siempre puede determinarse únicamente a partir de su contenido.

La configuración realizada por el usuario tendrá prioridad sobre la detección automática.

---

## 2.9 Fase 8 — Transformaciones

El usuario podrá aplicar transformaciones a los valores antes de realizar la conversión definitiva al tipo seleccionado.

En la primera versión se incorporarán transformaciones sencillas, como:

* Sustitución de texto.
* Eliminación de espacios innecesarios.
* Otras transformaciones básicas que se definan durante la implementación.

Por ejemplo, una columna podría contener:

```text
99.95 kg
101.20 kg
98.70 kg
```

El usuario podría configurar una sustitución para eliminar `kg`:

```text
Buscar: kg
Reemplazar por: [vacío]
```

Resultado:

```text
99.95
101.20
98.70
```

Después de la transformación, los valores podrán ser interpretados como números.

Las transformaciones deberán poder visualizarse antes de generar el Dataset.

---

## 2.10 Fase 9 — Validación

Una vez aplicadas las transformaciones y los tipos seleccionados, el sistema validará los valores resultantes.

La validación deberá comprobar que cada valor puede convertirse correctamente al tipo configurado.

Por ejemplo, si una columna está configurada como `Número`:

```text
99.95       ✓
101.20      ✓
98.70       ✓
abc         ✗
```

Los valores que no puedan convertirse correctamente deberán identificarse y mostrarse al usuario.

El sistema no deberá descartar ni modificar silenciosamente los valores problemáticos.

El usuario deberá poder volver a fases anteriores para corregir:

* El tipo seleccionado.
* La transformación.
* La definición de la tabla.

---

## 2.11 Fase 10 — Generación del Dataset

Cuando los datos hayan superado la validación, la aplicación generará el Dataset interno.

Este Dataset será la representación estructurada y normalizada de la información seleccionada.

Conceptualmente:

```text
Excel
  ↓
Hoja
  ↓
Tabla
  ↓
Columnas configuradas
  ↓
Transformaciones
  ↓
Validación
  ↓
Dataset
```

El Dataset deberá contener los valores preparados para ser utilizados por las funcionalidades posteriores de la aplicación.

A partir de este momento, las funcionalidades de análisis no necesitarán conocer la estructura concreta del archivo Excel original.

---

## 2.12 Fase 11 — Utilización del Dataset

Una vez generado el Dataset, el usuario podrá utilizarlo como fuente de información para las funcionalidades posteriores de Excel Dashboard.

Entre ellas:

* Visualización en tabla.
* Filtros.
* Ordenación.
* Agrupaciones.
* Gráficos.
* Informes.
* Exportaciones.

Estas funcionalidades se desarrollarán sobre el Dataset y no directamente sobre las celdas del Excel.

---

## 2.13 Flujo resumido

El proceso completo quedará definido de la siguiente manera:

```text
┌──────────────────────┐
│    Cargar Excel     │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│   Seleccionar hoja   │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│   Definir tabla      │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│   Previsualizar      │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│ Configurar columnas  │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│  Detectar tipos      │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│ Corregir tipos       │
│     (si procede)     │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│  Transformaciones    │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│     Validación       │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│      Dataset         │
└──────────┬───────────┘
           ↓
┌──────────────────────┐
│ Tabla / Filtros /    │
│ Gráficos / Informes  │
└──────────────────────┘
```

Cada fase deberá poder comunicar claramente al usuario qué se está procesando, qué decisiones ha tomado el sistema y qué decisiones debe tomar el propio usuario.

# 3. Fase de selección de hoja

## 3.1 Objetivo

La fase de selección de hoja permitirá al usuario decidir qué hojas del archivo Excel desea analizar y configurar.

El sistema no asumirá que todas las hojas de un mismo libro contienen información estructurada de la misma manera. Cada hoja podrá tener una finalidad y una estructura diferente.

Por este motivo, la configuración de una hoja se realizará de forma independiente del resto de hojas del libro.

---

## 3.2 Presentación de las hojas

Una vez cargado el archivo Excel, la aplicación mostrará un listado de las hojas disponibles.

Para cada hoja se podrá mostrar información básica como:

* Nombre de la hoja.
* Posición dentro del libro.
* Número aproximado de filas.
* Número aproximado de columnas.
* Estado de configuración.

Por ejemplo:

```text
CONTROL DE PESO Y TENSIÓN.xlsx

┌──────────────────────────────────────────────────────────┐
│ Histórico                                                │
│ 1178 filas · 27 columnas                                 │
│ Estado: Sin configurar                  [Configurar]     │
├──────────────────────────────────────────────────────────┤
│ Objetivos                                                │
│ 1004 filas · 28 columnas                                 │
│ Estado: Sin configurar                  [Configurar]     │
├──────────────────────────────────────────────────────────┤
│ Alimentación y ejercicio                                 │
│ 16 filas · 8 columnas                                    │
│ Estado: Sin configurar                  [Configurar]     │
├──────────────────────────────────────────────────────────┤
│ Ejercicios fuerza                                        │
│ 29 filas · 10 columnas                                   │
│ Estado: Sin configurar                  [Configurar]     │
└──────────────────────────────────────────────────────────┘
```

La información sobre filas y columnas tendrá carácter orientativo. El objetivo principal será ayudar al usuario a identificar rápidamente el contenido de cada hoja.

---

## 3.3 Estado de una hoja

Cada hoja podrá encontrarse en diferentes estados dentro del proceso.

Inicialmente estará en estado:

**Sin configurar**

Una vez que el usuario haya definido correctamente la tabla y completado el proceso de preparación, podrá pasar a estados como:

* Sin configurar.
* En configuración.
* Configurada.
* Con errores de validación.
* Dataset generado.

Los estados concretos podrán ajustarse durante la implementación.

---

## 3.4 Configuración independiente

Cada hoja tendrá su propia configuración.

Por ejemplo, un mismo archivo podría contener:

```text
CONTROL DE PESO Y TENSIÓN.xlsx

Histórico
    ↓
Dataset Histórico

Objetivos
    ↓
Dataset Objetivos

Alimentación y ejercicio
    ↓
Dataset Alimentación y ejercicio

Ejercicios fuerza
    ↓
Dataset Ejercicios fuerza
```

No será necesario que todas las hojas sean compatibles entre sí ni que tengan las mismas columnas.

Esto permitirá trabajar con archivos Excel que actúan como pequeños sistemas de información y que contienen diferentes tablas, plantillas, históricos o configuraciones dentro del mismo libro.

---

## 3.5 Selección de hojas

El usuario podrá decidir qué hojas desea procesar.

Una hoja que no resulte relevante podrá permanecer sin configurar y no formará parte del resultado del análisis.

Por ejemplo, si el usuario únicamente desea analizar el histórico de peso:

```text
[x] Histórico
[ ] Objetivos
[ ] Alimentación y ejercicio
[ ] Ejercicios fuerza
```

El sistema procesará únicamente la hoja seleccionada.

La posibilidad de configurar varias hojas permitirá posteriormente utilizar diferentes Datasets dentro de una misma sesión de análisis.

---

## 3.6 Una tabla por hoja en la primera versión

En la primera versión se establece como limitación funcional que una hoja podrá contener **una única tabla de datos configurable**.

Por tanto, si una hoja contiene varias tablas independientes, el usuario deberá seleccionar una de ellas para procesarla.

El sistema no intentará separar automáticamente varias tablas independientes dentro de la misma hoja.

Esta limitación simplifica el modelo inicial y permite concentrar el desarrollo en la correcta definición, interpretación y normalización de una tabla.

En versiones posteriores podrá estudiarse la posibilidad de detectar y procesar varias tablas dentro de una misma hoja.

---

## 3.7 Hojas que no contienen una tabla de datos

No todas las hojas de un archivo Excel tienen por qué contener datos adecuados para ser convertidos en un Dataset.

Una hoja puede utilizarse como:

* Portada.
* Leyenda.
* Configuración.
* Plantilla.
* Resumen.
* Hoja auxiliar.
* Información calculada.
* Contenido destinado únicamente a presentación.

El sistema no deberá considerar automáticamente que una hoja es válida simplemente porque contenga celdas con información.

El usuario será quien determine si desea intentar configurarla como tabla.

Por ejemplo, en el archivo de prueba:

```text
Histórico
→ Tabla de datos

Objetivos
→ Información estructurada y fórmulas

Alimentación y ejercicio
→ Planificación

Ejercicios fuerza
→ Plantilla / planificación de ejercicios
```

La aplicación podrá mostrar todas estas hojas, pero será el usuario quien decida cuáles tienen interés para el análisis.

---

## 3.8 Relación entre el archivo, las hojas y los Datasets

El archivo Excel será considerado el origen de los datos.

Cada hoja configurada podrá generar un Dataset independiente.

Conceptualmente:

```text
Archivo Excel
│
├── Hoja A
│    └── Dataset A
│
├── Hoja B
│    └── Dataset B
│
├── Hoja C
│    └── Dataset C
│
└── Hoja D
     └── Dataset D
```

Los Datasets pertenecerán al mismo proceso de importación, pero mantendrán su independencia estructural.

Esto permitirá que posteriormente una funcionalidad de análisis pueda trabajar:

* Sobre un único Dataset.
* Sobre varios Datasets relacionados, cuando exista una relación definida.
* Sobre el conjunto de Datasets disponibles dentro de un archivo.

La existencia de varios Datasets no implica que deban combinarse automáticamente.

---

## 3.9 Relación entre Datasets

La primera versión no realizará uniones automáticas entre Datasets procedentes de diferentes hojas.

Si dos Datasets contienen información relacionada, la aplicación no asumirá automáticamente cómo deben relacionarse.

Por ejemplo:

```text
Dataset Histórico
    fecha
    peso
    IMC

Dataset Objetivos
    indicador
    objetivo
```

Aunque ambos procedan del mismo Excel, no se combinarán automáticamente.

La posibilidad de establecer relaciones, realizar uniones o construir análisis utilizando varios Datasets podrá incorporarse posteriormente como una funcionalidad específica.

---

## 3.10 Reconfiguración de una hoja

El usuario podrá volver a configurar una hoja que ya haya sido procesada.

Esto permitirá modificar:

* Zona de la tabla.
* Encabezados.
* Columnas.
* Tipos de datos.
* Transformaciones.
* Opciones de validación.

Cuando se modifique una configuración, el Dataset asociado deberá considerarse pendiente de regeneración.

El sistema deberá evitar que el usuario trabaje con un Dataset antiguo sin saber que la configuración de origen ha cambiado.

---

## 3.11 Resultado de la fase

Al finalizar esta fase, el sistema deberá conocer:

* Qué hojas desea procesar el usuario.
* Qué hojas se han descartado.
* Qué hojas están pendientes de configuración.
* Qué hojas están configuradas.
* Qué hojas presentan problemas.
* Qué Datasets se han generado.

La selección de hojas dará paso a la **definición de la tabla** de cada hoja seleccionada.

El flujo será:

```text
Archivo Excel
      ↓
Listado de hojas
      ↓
Usuario selecciona hojas
      ↓
Configuración independiente
      ↓
Una tabla por hoja
      ↓
Definición de tabla
      ↓
Dataset independiente por hoja
```

# 4. Definición de tabla

## 4.1 Objetivo

La fase de definición de tabla tiene como objetivo determinar qué zona de una hoja Excel contiene los datos que el usuario desea procesar.

Una hoja Excel puede contener información adicional antes, después o alrededor de la tabla principal. Puede incluir títulos, leyendas, filas vacías, fórmulas, notas, gráficos u otras estructuras.

Por este motivo, Excel Dashboard no asumirá que toda la hoja representa una tabla.

El usuario deberá poder definir explícitamente qué zona de la hoja debe convertirse en Dataset.

---

## 4.2 Elementos que definen una tabla

En la primera versión, una tabla estará definida por los siguientes elementos:

* Fila de encabezados.
* Columna inicial.
* Columna final.
* Primera fila de datos.
* Última fila de datos.

Conceptualmente:

```text
              Columna inicial        Columna final
                    ↓                     ↓
              ┌─────────────────────────────┐
Fila encabezado│ Nombre │ Fecha │ Peso │ IMC │
              ├─────────────────────────────┤
Primera fila  │ Juan   │ ...   │ ...  │ ... │
de datos      │ ...    │ ...   │ ...  │ ... │
              │ ...    │ ...   │ ...  │ ... │
Última fila   │ ...    │ ...   │ ...  │ ... │
de datos      └─────────────────────────────┘
```

Estos límites determinarán las celdas que serán consideradas parte de la tabla.

---

## 4.3 Fila de encabezados

La fila de encabezados contendrá los nombres de las columnas que posteriormente formarán parte del Dataset.

Por ejemplo:

```text
A8       B8       C8          D8          E8
SEMANA   DÍA      DÍA SEMANA  MAÑANA/TARDE PESO (KG)
```

La aplicación deberá permitir seleccionar la fila que contiene los encabezados.

No se deberá asumir que los encabezados se encuentran necesariamente en la primera fila de la hoja.

En archivos reales pueden existir:

```text
Fila 1 → Título
Fila 2 → Leyenda
Fila 3 → Información auxiliar
...
Fila 8 → Encabezados
Fila 9 → Información adicional
Fila 10 → Datos
```

Por tanto, la posición de los encabezados será una propiedad configurable de la tabla.

---

## 4.4 Inicio de los datos

La primera fila de datos podrá ser diferente de la fila de encabezados.

Por defecto, el sistema podrá proponer como primera fila de datos la inmediatamente posterior a los encabezados.

Sin embargo, el usuario podrá modificarla.

Esto permitirá trabajar con estructuras como:

```text
Fila 8  → Encabezados
Fila 9  → Subencabezados / fórmulas / información auxiliar
Fila 10 → Primer dato
```

Como ocurre en determinadas partes del Excel de prueba.

La aplicación deberá permitir al usuario indicar que la fila 9 no forma parte de los datos aunque se encuentre inmediatamente después de los encabezados.

---

## 4.5 Límites de columnas

El usuario podrá indicar qué columnas forman parte de la tabla.

Por ejemplo:

```text
Columna inicial: A
Columna final: V
```

Esto permitirá excluir información auxiliar que se encuentre fuera de la tabla principal.

La selección podrá realizarse mediante:

* Introducción de letras de columna.
* Selección visual en la interfaz.
* Mecanismos automáticos de detección que posteriormente puedan ser corregidos por el usuario.

La primera versión podrá utilizar una interfaz sencilla si la selección visual resulta innecesariamente compleja en esta fase.

---

## 4.6 Última fila de datos

El usuario podrá establecer hasta qué fila deben procesarse los datos.

La aplicación podrá proponer automáticamente una última fila basándose en el contenido detectado.

No obstante, esta propuesta deberá poder modificarse.

Esto será especialmente importante en archivos que contengan:

* Filas vacías intermedias.
* Fórmulas más allá de los datos reales.
* Información auxiliar al final de la hoja.
* Formato aplicado a muchas filas aunque no existan datos.
* Filas que el usuario no desea incorporar.

---

## 4.7 Detección automática de la zona

La aplicación podrá intentar detectar automáticamente una posible tabla.

La detección podrá utilizar diferentes indicios, como:

* Filas con varios valores.
* Posibles nombres de columnas.
* Continuidad de los datos.
* Filas vacías.
* Tipos de contenido.
* Patrones repetitivos.
* Límites del área utilizada de la hoja.

El resultado será únicamente una **propuesta de configuración**.

El sistema no deberá asumir que una detección automática es necesariamente correcta.

El usuario podrá modificar todos los límites antes de continuar.

---

## 4.8 Previsualización de la zona seleccionada

Mientras el usuario configure la tabla, la aplicación deberá mostrar una previsualización de la zona seleccionada.

Por ejemplo:

```text
Hoja: Histórico

Encabezados: fila 8
Desde columna: A
Hasta columna: V
Datos desde: fila 10
Datos hasta: fila 1187

┌────────┬────────────┬──────────────┬──────────────┐
│ SEMANA │ DÍA        │ DÍA SEMANA   │ PESO (KG)    │
├────────┼────────────┼──────────────┼──────────────┤
│ 1      │ 45864      │ SABADO       │ -            │
│ 1      │ 45864      │ SABADO       │ 105.17       │
│ 1      │ 45864      │ SABADO       │ -            │
│ 1      │ 45870      │ VIERNES      │ -            │
│ 1      │ 45870      │ VIERNES      │ 103.10       │
└────────┴────────────┴──────────────┴──────────────┘

                         [Volver] [Confirmar tabla]
```

La previsualización será una herramienta de comprobación y no una modificación del archivo original.

---

## 4.9 Validación de la definición

Antes de permitir continuar, el sistema deberá comprobar que la definición de la tabla es coherente.

Como mínimo:

* La fila de encabezados debe existir.
* La primera fila de datos no puede estar fuera de los límites de la hoja.
* La última fila de datos debe ser igual o posterior a la primera.
* La columna final debe ser igual o posterior a la inicial.
* Debe existir al menos una columna.
* Debe existir al menos una fila de datos.

Si la configuración no es válida, la aplicación deberá informar del problema al usuario.

---

## 4.10 Nombres de las columnas

Los valores de la fila de encabezados se utilizarán inicialmente como nombres de las columnas.

Por ejemplo:

```text
SEMANA
DÍA
DÍA SEMANA
PESO (KG)
IMC
GRASA CORPORAL (%)
```

Durante esta fase no se realizarán todavía transformaciones complejas sobre los nombres.

Sin embargo, el sistema deberá contemplar situaciones como:

* Encabezados vacíos.
* Encabezados duplicados.
* Espacios innecesarios.
* Caracteres especiales.
* Encabezados excesivamente largos.

La normalización definitiva de los nombres de columnas se definirá en fases posteriores del proceso.

---

## 4.11 Celdas fuera de la tabla

Cualquier celda situada fuera de los límites definidos por el usuario quedará fuera del Dataset correspondiente.

Por ejemplo, si se define:

```text
A8:V1187
```

únicamente esa zona formará parte de la tabla.

La información existente en:

```text
W8, X8, ...
```

o en filas anteriores o posteriores no se incorporará al Dataset.

Esto permitirá trabajar con hojas que contienen información auxiliar sin necesidad de modificar el archivo Excel original.

---

## 4.12 Modificación de la definición

El usuario podrá modificar la definición de la tabla antes de continuar con la configuración de columnas.

Si la previsualización muestra que la selección no es correcta, podrá modificar:

* Fila de encabezados.
* Primera columna.
* Última columna.
* Primera fila de datos.
* Última fila de datos.

El sistema deberá actualizar la previsualización después de cada modificación relevante.

---

## 4.13 Limitación de la primera versión

La primera versión trabajará con una única tabla por hoja.

Si una hoja contiene varias tablas independientes:

```text
Tabla A

        espacio

Tabla B
```

el usuario deberá seleccionar una de ellas para procesarla.

No se intentará identificar ni combinar automáticamente varias tablas independientes dentro de la misma hoja.

Esta funcionalidad podrá estudiarse posteriormente cuando el modelo de Dataset y el sistema de detección de estructuras estén suficientemente definidos.

---

## 4.14 Resultado de la fase

Al finalizar esta fase, el sistema dispondrá de una definición inequívoca de la tabla que se desea procesar.

La definición tendrá conceptualmente esta estructura:

```text
Tabla
├── hoja
├── fila_encabezados
├── columna_inicial
├── columna_final
├── fila_inicio_datos
└── fila_fin_datos
```

Por ejemplo:

```text
Hoja: Histórico

Fila encabezados: 8
Columna inicial: A
Columna final: V
Inicio de datos: 10
Fin de datos: 1187
```

Esta definición será utilizada por las fases posteriores para extraer exclusivamente los datos seleccionados.

El flujo continuará con:

**Definición de tabla → Previsualización → Configuración de columnas**

# 5. Previsualización de datos

## 5.1 Objetivo

La fase de previsualización permitirá al usuario comprobar que la zona de datos seleccionada durante la definición de la tabla es correcta antes de iniciar el proceso de interpretación, detección de tipos y transformación.

La previsualización tendrá como objetivo principal facilitar la comprobación visual de los datos y detectar errores en la selección de la tabla.

Esta fase no modificará los datos originales del archivo Excel.

---

## 5.2 Datos mostrados

La previsualización mostrará una representación de la tabla definida por el usuario.

Como mínimo se mostrarán:

* Nombres de las columnas.
* Valores de las primeras filas de datos.
* Celdas vacías.
* Valores calculados de las fórmulas cuando estén disponibles.
* Información básica sobre la cantidad de filas y columnas seleccionadas.

La previsualización no tendrá que mostrar necesariamente todas las filas de la tabla, especialmente cuando el archivo contenga miles de registros.

Por ejemplo, una tabla de 1178 filas podrá mostrar inicialmente las primeras filas:

```text id="l2cr3n"
┌────────┬────────────┬──────────────┬────────────┬──────────┐
│ SEMANA │ DÍA        │ DÍA SEMANA   │ MOMENTO    │ PESO     │
├────────┼────────────┼──────────────┼────────────┼──────────┤
│ 1      │ 26/07/2025 │ SABADO       │ EN AYUNAS  │          │
│ 1      │ 26/07/2025 │ SABADO       │ MAÑANA     │ 105.17   │
│ 1      │ 26/07/2025 │ SABADO       │ TARDE      │          │
│ 1      │ 01/08/2025 │ VIERNES      │ EN AYUNAS  │          │
│ 1      │ 01/08/2025 │ VIERNES      │ MAÑANA     │ 103.10   │
└────────┴────────────┴──────────────┴────────────┴──────────┘

1178 filas · 22 columnas
```

La cantidad exacta de filas mostradas podrá definirse durante la implementación.

---

## 5.3 Número de filas mostradas

La previsualización deberá limitar el número de filas cargadas inicialmente para evitar que archivos grandes provoquen una interfaz lenta o innecesariamente pesada.

La primera versión podrá utilizar un número fijo de filas de muestra.

Posteriormente podrá incorporarse una previsualización más avanzada que permita:

* Navegar por diferentes partes de la tabla.
* Ir a una fila concreta.
* Mostrar las primeras y últimas filas.
* Cargar los datos progresivamente.

Estas mejoras no serán necesarias para el funcionamiento inicial del proceso de normalización.

---

## 5.4 Visualización de valores

La previsualización deberá mostrar los valores de una forma comprensible para el usuario.

Cuando una celda contenga una fórmula, se utilizará inicialmente su **valor calculado** para la representación visual siempre que dicho valor esté disponible.

Por ejemplo, una celda que contenga:

```text id="9q1kq8"
='Histórico'!E87 & "kg"
```

podrá mostrarse al usuario como:

```text
99.45kg
```

La fórmula original no se perderá. Se conservará internamente como parte de la información de origen de la celda.

Esto permitirá separar:

```text id="rj2u9u"
Información original
        ↓
Fórmula Excel
        ↓
Valor calculado
        ↓
Valor utilizado posteriormente
```

La forma exacta en que se mostrará esta información al usuario podrá ampliarse en fases posteriores.

---

## 5.5 Fechas

Las fechas almacenadas internamente por Excel podrán utilizar representaciones numéricas propias del formato Excel.

Por ejemplo, una fecha puede aparecer internamente como:

```text
45864
```

mientras que Excel la muestra como:

```text
26/07/2025
```

La previsualización deberá mostrar la fecha en un formato comprensible para el usuario siempre que el sistema pueda identificar correctamente que la celda representa una fecha.

La detección definitiva del tipo `Fecha` se realizará en la fase posterior de detección de tipos.

Por tanto, la presentación de una fecha y la decisión definitiva sobre su tipo serán conceptos independientes.

---

## 5.6 Valores vacíos

Las celdas vacías deberán mostrarse de forma diferenciada de los valores que contienen texto.

Por ejemplo:

```text id="8d9k20"
Valor vacío → [vacío]

Texto "-" → -

Texto "N/A" → N/A
```

No se deberán interpretar automáticamente como equivalentes durante la previsualización.

La decisión sobre si determinados valores como `-`, `N/A` o similares deben considerarse valores vacíos, texto u otra representación se realizará posteriormente durante la configuración y transformación de las columnas.

Esto es especialmente importante para la detección de tipos.

---

## 5.7 Valores problemáticos

La previsualización podrá identificar visualmente valores que puedan requerir atención, aunque no deberá realizar todavía la validación definitiva.

Por ejemplo:

```text id="w3yyf1"
PESO (KG)

105.17
103.10
-
abc
101.20
```

El valor `abc` podría ser relevante posteriormente si la columna es identificada como numérica.

En esta fase se mostrará el contenido tal como ha sido extraído, sin descartar automáticamente valores por no coincidir con el tipo que posteriormente pueda proponerse.

---

## 5.8 Información sobre la estructura

Además de los valores, la interfaz podrá mostrar información básica sobre la estructura seleccionada.

Por ejemplo:

```text id="9e4v5y"
Hoja: Histórico

Rango seleccionado: A8:V1187

Encabezados: fila 8
Primera fila de datos: 10
Última fila de datos: 1187

22 columnas
1178 filas
```

Esta información permitirá al usuario comprobar que la definición realizada en la fase anterior coincide con lo que esperaba.

---

## 5.9 Muestra de valores por columna

La previsualización podrá proporcionar posteriormente información adicional sobre cada columna, como:

* Número de valores no vacíos.
* Número de valores vacíos.
* Número de valores diferentes.
* Algunos valores representativos.

Esta información será especialmente útil para la fase de detección de tipos.

Por ejemplo:

```text id="y3o0b6"
PESO (KG)

Valores:       1.124
Vacíos:           54
Texto:              3
Números:        1.067

Tipo propuesto posteriormente: Número
```

No obstante, la primera versión podrá limitar la previsualización a la representación de los datos y dejar estos cálculos para la fase de detección de tipos.

---

## 5.10 Corrección de la selección

La previsualización deberá proporcionar una forma clara de volver a la definición de tabla.

Si el usuario observa que:

* Falta una columna.
* Se ha incluido una columna que no corresponde.
* La fila de encabezados es incorrecta.
* Se han incluido filas auxiliares.
* Faltan filas de datos.
* Se ha seleccionado una zona incorrecta.

podrá regresar a la fase anterior y modificar la definición.

El usuario no deberá tener que comenzar nuevamente desde la carga del archivo.

---

## 5.11 Confirmación de la tabla

Cuando el usuario considere correcta la previsualización, podrá confirmar la selección.

La confirmación permitirá avanzar a la configuración e interpretación de las columnas.

El flujo será:

```text id="02v7uo"
Definir tabla
      ↓
Previsualizar
      ↓
¿La selección es correcta?
      │
   ┌──┴──┐
   │     │
  NO     SÍ
   │     │
   ↓     ↓
Modificar  Configurar
tabla      columnas
```

La confirmación no modificará el archivo Excel original.

---

## 5.12 Principio de conservación del origen

La previsualización deberá basarse en los datos extraídos del archivo original sin sobrescribir ni alterar dicho archivo.

Durante el procesamiento podrán existir diferentes representaciones de un mismo valor:

```text
Valor almacenado
Valor de fórmula
Valor calculado
Valor transformado
Valor normalizado
```

Estas representaciones no deberán confundirse.

El sistema deberá mantener suficiente información para poder determinar, cuando sea necesario, de dónde procede un valor y qué transformaciones se han aplicado sobre él.

---

## 5.13 Resultado de la fase

Al finalizar la fase de previsualización deberán quedar confirmados:

* La hoja que se está procesando.
* La zona de tabla seleccionada.
* Los encabezados.
* El conjunto de filas de datos.
* El conjunto de columnas.
* La representación visual inicial de los datos.

El sistema estará entonces preparado para comenzar la **configuración y detección de tipos de las columnas**.

El flujo continuará con:

**Previsualización → Configuración de columnas → Detección de tipos**

# 6. Configuración de columnas

## 6.1 Objetivo

La fase de configuración de columnas permitirá al usuario revisar y configurar individualmente las columnas de la tabla antes de generar el Dataset.

El sistema proporcionará una propuesta inicial basada en el análisis realizado sobre los datos, pero el usuario mantendrá el control sobre la configuración definitiva.

Esta fase será el punto de unión entre la estructura original de la tabla y el modelo de datos que posteriormente utilizará el Dataset.

---

## 6.2 Identificación de las columnas

Cada columna de la tabla estará identificada inicialmente mediante el valor existente en la fila de encabezados.

Por ejemplo:

```text id="qj3k7p"
SEMANA
DÍA
DÍA SEMANA
MAÑANA/TARDE
PESO (KG)
IMC
GRASA CORPORAL (%)
AGUA CORPORAL (%)
...
```

Cada una de estas columnas tendrá una configuración independiente.

La configuración de una columna no deberá modificar directamente los valores de las demás columnas.

---

## 6.3 Información mostrada

Para cada columna, la interfaz deberá mostrar como mínimo:

* Nombre de la columna.
* Tipo detectado.
* Tipo utilizado actualmente.
* Algunos valores de ejemplo.
* Número de valores vacíos.
* Número de valores no vacíos.
* Posibles valores problemáticos.
* Transformaciones configuradas.

Una representación conceptual podría ser:

```text id="yq3o9e"
┌───────────────────────────────────────────────┐
│ PESO (KG)                                     │
├───────────────────────────────────────────────┤
│ Tipo detectado:     Número                    │
│ Tipo utilizado:    [ Número ▼ ]               │
│                                               │
│ Valores de ejemplo:                           │
│ 105.17                                        │
│ 103.10                                        │
│ 99.45                                         │
│ -                                             │
│                                               │
│ Valores: 1.124                                │
│ Vacíos: 54                                    │
│                                               │
│ Transformaciones                              │
│ └─ Ninguna                                    │
│                                               │
│             [ + Añadir transformación ]        │
└───────────────────────────────────────────────┘
```

La información estadística exacta podrá ampliarse durante la implementación.

---

## 6.4 Tipo detectado y tipo utilizado

La aplicación distinguirá entre:

**Tipo detectado**

Tipo que el sistema propone automáticamente después de analizar los valores de la columna.

**Tipo utilizado**

Tipo que finalmente se utilizará para construir el Dataset.

Por ejemplo:

```text id="r0d7n3"
Tipo detectado: Número

Tipo utilizado:
[ Número ▼ ]
```

Si el usuario considera que la propuesta es incorrecta, podrá modificar el tipo utilizado:

```text id="g3v1c5"
Tipo detectado: Número

Tipo utilizado:
[ Texto ▼ ]
```

La decisión manual del usuario tendrá prioridad sobre la detección automática.

---

## 6.5 Tipos disponibles

La primera versión utilizará los siguientes tipos:

### Texto

Para valores que deben conservarse como texto.

Ejemplos:

```text
SABADO
EN AYUNAS
TELMISARTAN 40mg
CAMINAR 7,79 KM EN 1H27M
```

### Número

Para valores numéricos que puedan utilizarse posteriormente en operaciones, filtros, estadísticas o gráficos.

Ejemplos:

```text
105.17
32.1
2047
22.5
```

### Fecha

Para valores que representan fechas.

Ejemplo:

```text
26/07/2025
01/08/2025
```

### Booleano

Para valores que representan dos estados.

Ejemplos:

```text
TRUE
FALSE
```

También podrá estudiarse posteriormente el reconocimiento de representaciones textuales habituales como `Sí/No`, `Activo/Inactivo` o equivalentes.

---

## 6.6 Valores de ejemplo

La interfaz deberá mostrar una muestra representativa de los valores de cada columna.

El objetivo será permitir que el usuario compruebe rápidamente si la interpretación propuesta tiene sentido.

Por ejemplo:

```text id="h0v2ne"
Columna: PESO (KG)

Tipo detectado: Número

Muestra:

105.17
103.10
101.85
99.45
-
98.90
```

La muestra no tendrá que contener todos los valores de la columna.

El análisis completo se realizará sobre el conjunto de datos, mientras que la interfaz utilizará una muestra para facilitar la revisión.

---

## 6.7 Valores vacíos

La configuración de una columna deberá distinguir entre:

* Celdas realmente vacías.
* Valores almacenados como texto.
* Valores especiales utilizados por el usuario para representar ausencia de información.

Por ejemplo:

```text
[vacío]
-
N/A
No disponible
```

Estos valores no se considerarán automáticamente equivalentes.

La posibilidad de definir qué valores deben tratarse como ausencia de información podrá formar parte del sistema de transformaciones.

---

## 6.8 Configuración manual

El usuario podrá modificar las decisiones automáticas del sistema.

Como mínimo podrá modificar:

* Tipo de dato.
* Transformaciones.
* Tratamiento de determinados valores especiales, cuando esta funcionalidad esté disponible.

La aplicación deberá mostrar claramente cuándo una configuración ha sido modificada manualmente.

Por ejemplo:

```text id="xv4xid"
Tipo detectado: Número
Tipo utilizado: Texto

Configuración manual ✓
```

Esto permitirá diferenciar una decisión automática de una decisión explícita del usuario.

---

## 6.9 Transformaciones asociadas a una columna

Las transformaciones estarán asociadas a una columna concreta.

Por ejemplo:

```text id="3w2m6d"
Columna: PESO (KG)

Transformaciones:

1. Buscar: "kg"
   Reemplazar por: ""

2. Eliminar espacios
```

Las transformaciones se aplicarán en el orden definido.

El sistema deberá conservar la configuración para poder reproducir posteriormente el mismo proceso.

---

## 6.10 Orden de las transformaciones

Cuando una columna tenga varias transformaciones, el orden será significativo.

Por ejemplo:

```text id="f1r0l2"
Valor original:
" 99.95 kg "

Transformación 1:
Eliminar espacios exteriores

Resultado:
"99.95 kg"

Transformación 2:
Eliminar "kg"

Resultado:
"99.95"

Transformación 3:
Convertir a número

Resultado:
99.95
```

La conversión al tipo seleccionado se considerará parte del proceso de normalización y deberá producirse después de las transformaciones que sean necesarias para permitir dicha conversión.

La interfaz deberá representar el orden de las transformaciones de forma clara.

---

## 6.11 Previsualización de la transformación

El usuario deberá poder comprobar el efecto de una transformación antes de aplicarla definitivamente al Dataset.

Por ejemplo:

```text id="6v2wzq"
Valor original       Resultado

"99.95 kg"      →    "99.95"
"101.20 kg"     →    "101.20"
"98.70 kg"      →    "98.70"
```

Esta previsualización permitirá detectar errores en configuraciones como una sustitución demasiado amplia o una transformación que produzca resultados inesperados.

---

## 6.12 No modificación del origen

La configuración de una columna no modificará el archivo Excel original.

Las transformaciones se realizarán sobre una representación interna de los datos.

Conceptualmente:

```text id="u4n4cr"
Excel original
      │
      ├── Valor original
      │
      └── Representación interna
                │
                ├── Transformación 1
                ├── Transformación 2
                └── Conversión de tipo
                         ↓
                   Valor normalizado
```

Esto permitirá volver a configurar la columna sin tener que modificar o regenerar el archivo Excel de origen.

---

## 6.13 Columnas complejas

No todas las columnas podrán convertirse directamente en un tipo numérico o de fecha.

Por ejemplo:

```text
TENSIÓN ARTERIAL

205/123 mmHg, 199/119 mmHg
125/85
115/75
```

En la primera versión, una columna de este tipo podrá mantenerse como **Texto**.

No será obligatorio que el sistema interprete automáticamente el significado interno de todos los datos.

En versiones posteriores podrán desarrollarse transformaciones o tipos semánticos específicos para determinados patrones.

---

## 6.14 Columnas calculadas mediante fórmulas

Una columna puede contener valores procedentes de fórmulas Excel.

La configuración deberá trabajar inicialmente con el resultado calculado de la fórmula cuando este esté disponible.

Por ejemplo, una fórmula como:

```text
='Histórico'!E87 & "kg"
```

puede producir:

```text
99.45kg
```

La aplicación podrá analizar el resultado `99.45kg` para determinar una propuesta de tipo, sin perder la información de que el valor original procedía de una fórmula.

La gestión avanzada de dependencias entre fórmulas y hojas quedará fuera del alcance de esta fase.

---

## 6.15 Columnas sin nombre

Si una columna seleccionada no dispone de un encabezado válido, el sistema deberá asignarle temporalmente un identificador que permita trabajar con ella.

Por ejemplo:

```text
Columna A
Columna B
Columna C
```

o un identificador interno equivalente.

Posteriormente podrá solicitarse al usuario que proporcione un nombre válido antes de generar el Dataset.

La estrategia definitiva para normalizar nombres de columnas se definirá durante la especificación técnica.

---

## 6.16 Encabezados duplicados

Si existen dos o más columnas con el mismo nombre:

```text
Fecha
Peso
Peso
IMC
```

el sistema deberá detectar la duplicidad.

No deberá sobrescribir una columna con otra ni perder información.

Antes de generar el Dataset, deberá resolverse la duplicidad mediante una estrategia que se definirá posteriormente, por ejemplo:

```text
Peso
Peso_2
```

o mediante una intervención del usuario.

La decisión concreta se establecerá en la fase de normalización de nombres.

---

## 6.17 Estado de configuración de la tabla

La tabla podrá considerarse lista para continuar cuando todas sus columnas tengan:

* Un nombre válido o un identificador provisional aceptable.
* Un tipo seleccionado.
* Sus transformaciones configuradas, si las necesita.
* Una configuración coherente.

La validación de si los valores cumplen realmente el tipo seleccionado se realizará en la fase específica de validación.

---

## 6.18 Resultado de la fase

Al finalizar la configuración de columnas, el sistema dispondrá de una definición para cada columna de la tabla.

Conceptualmente:

```text id="7xw8mm"
Columna
├── nombre
├── tipo_detectado
├── tipo_utilizado
├── valores_muestra
├── transformaciones
└── configuración
```

Por ejemplo:

```text id="5d8f4h"
{
    nombre: "PESO (KG)",
    tipo_detectado: "number",
    tipo_utilizado: "number",
    transformaciones: []
}
```

O:

```text id="t9j2kv"
{
    nombre: "PESO",
    tipo_detectado: "text",
    tipo_utilizado: "number",
    transformaciones: [
        eliminar "kg"
    ]
}
```

Esta configuración será utilizada por las fases posteriores de transformación y validación.

El flujo continuará con:

**Configuración de columnas → Detección de tipos → Transformaciones → Validación**

# 7. Detección de tipos de datos

## 7.1. Objetivo

Una vez definida la tabla de una hoja, el sistema analizará los valores de cada columna para proponer automáticamente el tipo de dato que mejor representa su contenido.

La detección tendrá como finalidad facilitar la configuración posterior de la columna, pero **no sustituirá la decisión del usuario**.

El sistema deberá diferenciar entre:

* el tipo interno con el que Excel almacena una celda;
* el valor calculado cuando la celda contiene una fórmula;
* el tipo de dato que representa conceptualmente ese valor dentro del Dataset.

Por tanto, la detección no se basará únicamente en el tipo devuelto por la librería de lectura de Excel.

Ejemplo:

```text
Celda Excel:
=TRUE()

Tipo interno:
Fórmula

Valor calculado:
true

Tipo propuesto para el Dataset:
Boolean
```

De la misma forma, una fecha de Excel puede estar almacenada internamente como un número, pero ser interpretada como una fecha cuando su formato y contexto así lo indiquen.

---

## 7.2. Tipos disponibles en la primera versión

La primera versión del sistema trabajará únicamente con cuatro tipos de datos:

| Tipo    | Descripción                                                                     |
| ------- | ------------------------------------------------------------------------------- |
| Text    | Texto libre o valores que no pueden clasificarse de forma segura como otro tipo |
| Number  | Valores numéricos                                                               |
| Date    | Fechas                                                                          |
| Boolean | Valores verdadero/falso                                                         |

No se crearán inicialmente tipos semánticos adicionales como:

* porcentaje;
* moneda;
* peso;
* distancia;
* duración;
* temperatura;
* presión arterial;
* rango;
* intervalo;
* porcentaje con unidad;
* cantidad con unidad.

Estos conceptos podrán incorporarse posteriormente como una capa semántica sobre los tipos básicos.

Por ejemplo:

```text
31,7 %
```

podrá terminar representándose como un `Number` cuyo valor sea:

```text
0.317
```

pero la interpretación específica como **porcentaje** quedará fuera de la primera versión.

---

## 7.3. Análisis de toda la columna

La detección deberá analizar los valores de **todas las filas de datos de la columna**, no únicamente la primera celda ni una muestra reducida.

Esto es necesario porque una misma columna puede contener:

* números;
* textos;
* valores vacíos;
* valores especiales;
* fórmulas;
* fechas;
* valores que representan errores o excepciones.

Por ejemplo:

```text
PESO
----------------
105.17
103.10
-
102.80
103.25
```

El hecho de que una de las celdas contenga `-` no significa necesariamente que toda la columna sea de tipo `Text`.

El sistema deberá considerar el conjunto de valores antes de realizar la propuesta.

---

## 7.4. Información recopilada durante el análisis

Durante la detección se recopilará información suficiente para justificar la propuesta realizada.

Como mínimo, para cada columna se deberá conocer:

* número total de filas analizadas;
* número de celdas vacías;
* número de valores no vacíos;
* número de valores identificados como posibles números;
* número de valores identificados como posibles fechas;
* número de valores identificados como posibles booleanos;
* número de valores identificados como texto;
* presencia de fórmulas;
* presencia de valores potencialmente problemáticos;
* formato de las celdas cuando aporte información relevante.

Esta información permitirá que la aplicación no se limite a mostrar:

```text
Tipo detectado: Number
```

sino que pueda proporcionar información contextual como:

```text
Tipo detectado: Number

Valores analizados: 1.178
Valores numéricos: 1.105
Valores vacíos: 58
Valores especiales: 15
```

La interfaz podrá utilizar posteriormente esta información para mostrar advertencias o facilitar la revisión manual.

---

## 7.5. Valores vacíos

Las celdas realmente vacías no deberán utilizarse como evidencia para determinar el tipo de una columna.

Por ejemplo:

```text
100.20
101.30
[vacío]
102.10
[vacío]
```

deberá seguir pudiendo detectarse como:

```text
Number
```

Los valores vacíos, no obstante, deberán contabilizarse y conservarse como información relevante para las fases posteriores de validación y generación del Dataset.

---

## 7.6. Valores especiales

Determinados valores pueden representar la ausencia de información sin estar realmente vacíos.

Ejemplos:

```text
-
N/A
NA
SIN DATOS
NO DISPONIBLE
```

Estos valores no deberán considerarse automáticamente equivalentes a una celda vacía.

En la primera versión se conservará la diferencia entre:

```text
celda vacía
```

y:

```text
valor textual utilizado como marcador
```

La configuración de cómo tratar estos valores se realizará posteriormente mediante la configuración de la columna y/o las transformaciones.

Esto permite evitar conversiones automáticas que puedan modificar el significado original del Excel.

---

## 7.7. Detección de números

Una columna podrá proponerse como `Number` cuando sus valores puedan interpretarse de forma consistente como valores numéricos.

Se deberán contemplar tanto números almacenados directamente como números que puedan aparecer en determinadas circunstancias mediante fórmulas.

Ejemplo:

```text
105.17
103.10
99.45
100.20
```

→ `Number`

Sin embargo, un valor como:

```text
99.95 kg
```

se considerará inicialmente `Text`.

El sistema no deberá eliminar automáticamente unidades, símbolos o texto adicional para convertir un valor en número.

La conversión:

```text
"99.95 kg"
       ↓
"99.95"
       ↓
99.95
```

se realizará posteriormente mediante una transformación configurada por el usuario.

De esta manera se mantiene la separación entre:

```text
detección
```

y:

```text
transformación
```

---

## 7.8. Detección de fechas

Las fechas requieren un tratamiento específico porque Excel puede almacenar una fecha internamente como un número.

Por ejemplo:

```text
45864
```

puede ser internamente un valor numérico, pero representar una fecha.

Para detectar este caso se tendrá en cuenta, entre otros elementos:

* el valor almacenado;
* el formato de la celda;
* la posibilidad de conversión a una fecha válida;
* la coherencia con el resto de valores de la columna.

Por tanto:

```text
45864
45870
45871
```

no deberá clasificarse automáticamente como `Number` si las celdas están configuradas y utilizadas como fechas.

La detección deberá distinguir entre:

```text
Número real
```

y:

```text
Número utilizado internamente por Excel para representar una fecha.
```

La representación visual de la fecha y su almacenamiento interno no deben confundirse.

---

## 7.9. Detección de valores booleanos

Se considerará `Boolean` cuando los valores representen de forma clara estados verdadero/falso.

Ejemplos:

```text
TRUE
FALSE
```

o valores equivalentes obtenidos como resultado de fórmulas.

En particular, una celda que contenga:

```text
=TRUE()
```

deberá poder detectarse como `Boolean` a partir de su valor calculado, aunque internamente Excel la identifique como una fórmula.

El sistema deberá conservar simultáneamente la información original de la fórmula.

---

## 7.10. Celdas con fórmulas

Las fórmulas no constituirán por sí mismas un tipo de dato.

Una fórmula podrá devolver:

* un número;
* un texto;
* una fecha;
* un booleano;
* un valor vacío;
* otro resultado interpretable.

Por ello, para la detección semántica se utilizará preferentemente el **valor calculado de la fórmula**, manteniendo almacenada la fórmula original.

Ejemplo:

```text
Fórmula:
='Histórico'!E87 & "kg"

Valor calculado:
99.45kg

Tipo Excel:
Fórmula

Tipo propuesto:
Text
```

La aplicación conservará la información de origen para evitar perder datos relevantes.

Conceptualmente:

```text
valor original
        +
valor calculado
        ↓
detección del tipo
```

---

## 7.11. Columnas mixtas

Una columna podrá contener valores que no sean completamente homogéneos.

Ejemplo:

```text
105.17
103.10
-
102.80
N/A
```

El sistema deberá analizar el conjunto y determinar si existe suficiente evidencia para proponer un tipo.

Cuando exista una combinación de valores que pueda interpretarse razonablemente como un tipo determinado junto con valores especiales, estos últimos podrán considerarse valores excepcionales y quedar señalados para la validación posterior.

En cambio, cuando exista una mezcla significativa de tipos incompatibles, la columna podrá proponerse como `Text` o quedar marcada para revisión.

La primera versión deberá priorizar **no realizar conversiones incorrectas** frente a intentar interpretar automáticamente todos los casos posibles.

---

## 7.12. Valores numéricos almacenados como texto

Un valor visualmente numérico no deberá convertirse automáticamente en `Number` únicamente porque contenga caracteres que parezcan un número.

Por ejemplo:

```text
"99.95"
```

puede ser un texto en el Excel.

El sistema podrá identificarlo como candidato a número, pero deberá distinguir entre:

```text
Number
```

y:

```text
Text con contenido numérico
```

La conversión definitiva deberá formar parte del proceso de normalización.

Esto permitirá que el sistema mantenga el valor original y haga explícita cualquier conversión.

---

## 7.13. Propuesta de tipo y tipo utilizado

La detección generará un:

```text
tipo_detectado
```

que será la propuesta automática del sistema.

Posteriormente el usuario podrá establecer:

```text
tipo_utilizado
```

Ejemplo:

```text
Columna: PESO (KG)

Tipo detectado: Text
Tipo utilizado: Number
```

En este caso, el sistema deberá requerir o aplicar las transformaciones necesarias para que los valores puedan convertirse correctamente al tipo seleccionado.

La decisión manual del usuario tendrá prioridad sobre la propuesta automática.

Por tanto:

```text
detección automática
        ↓
propuesta
        ↓
revisión del usuario
        ↓
tipo utilizado
```

---

## 7.14. Justificación de la detección

La interfaz deberá permitir conocer por qué se ha realizado una determinada propuesta.

Por ejemplo:

```text
Tipo detectado: Number

Motivo:
La mayoría de los valores no vacíos pueden
interpretarse como valores numéricos.

Valores analizados: 1.120
Valores numéricos: 1.105
Valores vacíos: 15
Valores no reconocidos: 0
```

Para una columna de fecha:

```text
Tipo detectado: Date

Motivo:
Los valores corresponden a fechas válidas
y las celdas utilizan un formato de fecha.
```

La finalidad es que el sistema sea transparente y que el usuario pueda revisar sus decisiones.

---

## 7.15. Detección frente a transformación

La detección y la transformación serán procesos independientes.

La detección responderá a:

> ¿Qué tipo parece tener este valor o columna actualmente?

La transformación responderá a:

> ¿Qué debo hacer con este valor para obtener el tipo que necesito?

Ejemplo:

```text
Excel
"99.95 kg"

        ↓

Detección
Text

        ↓

Transformación
Eliminar "kg"

        ↓

Conversión
Number

        ↓

Dataset
99.95
```

Esto evita que el detector modifique silenciosamente los datos para conseguir que encajen en un tipo.

---

## 7.16. Resultado de la fase de detección

Una vez analizada cada columna, el sistema deberá disponer de una configuración conceptual similar a:

```text
Columna
├── nombre
├── tipo_detectado
├── evidencias
├── valores_muestra
├── vacíos
├── valores_especiales
└── tipo_utilizado
```

El resultado no será todavía el Dataset definitivo.

La detección únicamente proporcionará la información necesaria para que el usuario pueda revisar y configurar correctamente cada columna.

El flujo continuará posteriormente con:

```text
Detección automática
        ↓
Propuesta de tipo
        ↓
Revisión del usuario
        ↓
Selección del tipo utilizado
        ↓
Transformaciones
        ↓
Validación
        ↓
Dataset normalizado
```

La generación del Dataset no se realizará hasta que la configuración de las columnas y sus transformaciones hayan sido validadas.

# 8. Transformaciones y normalización de datos

## 8.1. Objetivo

Las transformaciones y la normalización tienen como objetivo preparar los valores de las columnas para que puedan incorporarse correctamente al Dataset interno.

La detección de tipos realizada en la fase anterior determina qué tipo de dato parece tener una columna, mientras que esta fase permite **modificar la representación de los valores para adaptarlos al tipo seleccionado**.

El proceso deberá ser explícito y controlado por el usuario.

Por ejemplo:

```text id="x9f3ka"
Valor original:
" 99.95 kg "

        ↓

Transformación:
Eliminar espacios exteriores

        ↓

"99.95 kg"

        ↓

Transformación:
Eliminar "kg"

        ↓

"99.95"

        ↓

Conversión:
Number

        ↓

Valor normalizado:
99.95
```

El valor original deberá conservarse siempre.

---

## 8.2. Separación entre dato original y dato normalizado

El sistema no modificará directamente el archivo Excel ni sustituirá los valores originales.

Para cada valor que sea transformado conceptualmente deberán distinguirse:

```text id="5f8q2m"
Valor original
      ↓
Transformaciones
      ↓
Valor transformado
      ↓
Conversión al tipo seleccionado
      ↓
Valor normalizado
```

Esto permitirá conocer posteriormente:

* qué valor contenía originalmente el Excel;
* qué transformaciones se aplicaron;
* cuál fue el resultado;
* qué valor terminó incorporándose al Dataset.

Ejemplo:

```text id="0f6f0j"
Original:
" 99.95 kg "

Transformaciones:
1. trim
2. remove "kg"

Resultado:
"99.95"

Tipo:
Number

Valor normalizado:
99.95
```

---

## 8.3. Las transformaciones pertenecen a la columna

Las transformaciones se configurarán a nivel de columna.

Una columna podrá tener:

* ninguna transformación;
* una transformación;
* varias transformaciones encadenadas.

Ejemplo:

```text id="5t8g3w"
Columna:
PESO

Transformaciones:

1. Eliminar espacios exteriores
2. Sustituir "," por "."
3. Eliminar "kg"
4. Convertir a Number
```

El orden será importante, ya que una transformación puede modificar el valor que recibirá la siguiente.

---

## 8.4. Transformaciones iniciales

La primera versión incorporará únicamente transformaciones sencillas y previsibles.

Entre ellas se contemplan:

### 8.4.1. Eliminar espacios exteriores

Elimina espacios al principio y al final del valor.

```text id="r1c6vp"
" 99.95 kg "
        ↓
"99.95 kg"
```

---

### 8.4.2. Buscar y reemplazar

Permite sustituir una cadena por otra.

Ejemplo:

```text id="5xq2lc"
Buscar:
kg

Reemplazar:
[vacío]
```

Resultado:

```text id="p7h2ak"
"99.95 kg"
        ↓
"99.95"
```

También podrá utilizarse para normalizar valores textuales.

Ejemplo:

```text id="r8m0sq"
"Sí"  →  "TRUE"
"SI"  →  "TRUE"
"No"  →  "FALSE"
```

Siempre que el usuario configure expresamente dicha transformación.

---

### 8.4.3. Sustitución de separadores

Podrá utilizarse para normalizar representaciones numéricas.

Por ejemplo:

```text id="w3p7dn"
"99,95"
        ↓
"99.95"
```

Esta transformación será especialmente útil para datos procedentes de formatos regionales diferentes.

---

### 8.4.4. Conversión de tipo

Una vez realizadas las transformaciones necesarias, el sistema podrá convertir el resultado al tipo seleccionado.

Ejemplo:

```text id="9z1v8q"
"99.95"
        ↓
Number
        ↓
99.95
```

La conversión solamente deberá realizarse después de que el valor cumpla las condiciones necesarias para el tipo seleccionado.

---

## 8.5. Transformaciones encadenadas

Las transformaciones deberán poder ejecutarse en un orden determinado.

Ejemplo:

```text id="m9k2qa"
Valor original:

" 99,95 kg "

        ↓ trim

"99,95 kg"

        ↓ replace "," → "."

"99.95 kg"

        ↓ replace "kg" → ""

"99.95"

        ↓ convert Number

99.95
```

El usuario deberá poder visualizar el resultado antes de confirmar la configuración.

Esto permitirá detectar errores derivados del orden de las transformaciones.

---

## 8.6. Previsualización de las transformaciones

Antes de generar el Dataset, la aplicación deberá mostrar una previsualización del resultado de las transformaciones.

Como mínimo, podrá mostrarse:

| Original    | Transformado | Tipo final |
| ----------- | ------------ | ---------- |
| `99.95 kg`  | `99.95`      | Number     |
| `103.10 kg` | `103.10`     | Number     |
| `102.80 kg` | `102.80`     | Number     |

La previsualización permitirá comprobar que la configuración produce el resultado esperado sin necesidad de generar todavía el Dataset definitivo.

---

## 8.7. Transformaciones y valores vacíos

Las transformaciones deberán diferenciar entre:

* celda realmente vacía;
* cadena vacía;
* valor textual;
* marcador especial como `-` o `N/A`.

Por ejemplo:

```text id="0v4d2j"
[vacío]
```

no deberá convertirse automáticamente en:

```text id="2o4r8s"
0
```

Un valor vacío representa ausencia de información y deberá mantenerse como tal salvo que el usuario configure expresamente otro comportamiento.

---

## 8.8. Transformaciones y valores especiales

Los valores como:

```text id="5b2j7c"
-
N/A
SIN DATOS
NO DISPONIBLE
```

podrán ser tratados mediante transformaciones posteriores.

Por ejemplo:

```text id="x3n9qc"
"-"
  ↓
[vacío]
```

Esto permitirá que una columna numérica pueda contener valores ausentes sin obligar a convertirlos en texto.

La decisión deberá ser explícita para evitar interpretar incorrectamente información válida.

---

## 8.9. Conversión a número

Una columna seleccionada como `Number` deberá terminar generando valores numéricos válidos o valores ausentes permitidos.

Ejemplo:

```text id="w2f4gc"
"99.95"
"103.10"
"102.80"
```

se convertirá en:

```text id="m5a7hx"
99.95
103.10
102.80
```

Si un valor no puede convertirse correctamente, el sistema no deberá inventar ni modificar el dato automáticamente.

Deberá producirse una incidencia de validación.

Ejemplo:

```text id="g7q4mn"
Columna: PESO

Valor:
"aproximadamente 100 kg"

Resultado:
No se puede convertir a Number
```

El usuario podrá entonces modificar la transformación o cambiar el tipo utilizado.

---

## 8.10. Conversión a fecha

La normalización de fechas deberá producir un valor de fecha coherente dentro del Dataset.

El sistema deberá ser capaz de trabajar tanto con fechas almacenadas internamente por Excel como con valores textuales que puedan convertirse mediante una transformación válida.

Ejemplo:

```text id="k8p1wr"
Valor Excel:
45864

Tipo detectado:
Date

Valor normalizado:
Fecha correspondiente
```

No deberá confundirse el número interno utilizado por Excel con el significado final de la información.

---

## 8.11. Conversión a booleano

Los valores destinados a una columna `Boolean` deberán normalizarse a un conjunto consistente de valores verdadero/falso.

Por ejemplo, mediante una configuración explícita:

```text id="v2d9sa"
"SI" → TRUE
"SÍ" → TRUE
"NO" → FALSE
```

La aplicación no deberá asumir automáticamente que cualquier texto similar representa un booleano.

Cuando existan valores que no puedan interpretarse correctamente, deberán quedar señalados para validación.

---

## 8.12. Normalización de nombres de columnas

Además de los valores, los nombres de las columnas podrán requerir normalización.

Por ejemplo:

```text id="z7x1qe"
"PESO (KG)"
"Peso (Kg)"
" peso (kg) "
```

podrían necesitar una representación interna consistente.

La primera versión deberá separar:

```text id="g0b5nc"
nombre mostrado
```

de:

```text id="4s7m2k"
identificador interno
```

El nombre original deberá conservarse para mantener la trazabilidad con el Excel.

La estrategia concreta de generación de identificadores, tratamiento de duplicados y caracteres especiales se definirá en la especificación técnica.

---

## 8.13. Transformaciones no destructivas

Las transformaciones nunca deberán modificar el archivo fuente.

El flujo será:

```text id="h8s2fd"
Archivo Excel original
        │
        ├── Datos originales
        │
        └── Configuración de transformaciones
                    ↓
              Datos normalizados
                    ↓
                 Dataset
```

Esto permitirá volver a procesar el mismo Excel con una configuración diferente.

También permitirá modificar posteriormente las transformaciones sin haber perdido la información original.

---

## 8.14. Repetibilidad del proceso

La configuración de transformaciones deberá poder guardarse como parte de la configuración del Dataset.

De esta forma, si el usuario vuelve a procesar un archivo con la misma estructura, el sistema podrá aplicar las mismas reglas.

Conceptualmente:

```text id="j5c9qx"
Excel
+
Configuración
+
Transformaciones
        ↓
Dataset
```

Esto será especialmente importante en una futura evolución del proyecto hacia procesos periódicos o automatizados.

La automatización no forma parte del MVP, pero la arquitectura deberá evitar que las transformaciones dependan exclusivamente de acciones manuales imposibles de reproducir.

---

## 8.15. Errores durante la normalización

Si una transformación o conversión produce un resultado inválido, el sistema deberá conservar información suficiente para identificar el problema.

Ejemplo:

```text id="4g6v2t"
Columna:
PESO

Valor original:
"abc kg"

Transformación:
Eliminar "kg"

Resultado:
"abc"

Conversión:
Number

Resultado:
ERROR
```

La incidencia deberá poder asociarse como mínimo a:

* hoja;
* columna;
* fila;
* valor original;
* transformación aplicada;
* resultado obtenido;
* motivo del error.

El sistema no deberá ocultar estos errores ni sustituir automáticamente el valor por otro.

---

## 8.16. Estado de una columna tras la normalización

Una columna podrá encontrarse conceptualmente en uno de estos estados:

```text id="8k3q2z"
Sin configurar
      ↓
Configurada
      ↓
Transformaciones definidas
      ↓
Normalización correcta
      ↓
Lista para Dataset
```

O bien:

```text id="x2m7vp"
Configurada
      ↓
Transformación
      ↓
Error de normalización
      ↓
Revisión necesaria
```

Una columna con errores que impidan garantizar su tipo no deberá considerarse preparada para la generación definitiva del Dataset.

---

## 8.17. Resultado de la fase

Al finalizar esta fase, cada columna deberá tener definida, cuando corresponda:

```text id="p4s8kc"
Columna
├── nombre original
├── nombre interno
├── tipo detectado
├── tipo utilizado
├── transformaciones
├── reglas de normalización
└── estado de validación
```

Y cada valor podrá representarse conceptualmente como:

```text id="q6v1ra"
Valor original
      ↓
Transformaciones
      ↓
Valor transformado
      ↓
Conversión de tipo
      ↓
Valor normalizado
```

El resultado de esta fase será la información preparada para la siguiente etapa: **validación de los datos y de la configuración antes de generar el Dataset**.

---

## 8.18. Principios de la normalización

La primera versión seguirá los siguientes principios:

1. **No modificar el Excel original.**
2. **Conservar el valor original.**
3. **No realizar conversiones silenciosas.**
4. **Las transformaciones serán explícitas.**
5. **El orden de las transformaciones será significativo.**
6. **El usuario podrá previsualizar el resultado.**
7. **Los errores de conversión deberán ser visibles.**
8. **Los valores vacíos no se convertirán automáticamente en cero u otro valor.**
9. **Los valores especiales no se considerarán automáticamente vacíos.**
10. **La normalización deberá poder reproducirse a partir de la configuración.**

El objetivo no es modificar el Excel para que encaje en el sistema, sino **crear una representación normalizada y controlada de los datos originales que pueda utilizarse de forma fiable dentro del Dataset**.

# 9. Validación de datos y configuración

## 9.1. Objetivo

La fase de validación tiene como objetivo comprobar que la tabla configurada, los tipos seleccionados y las transformaciones aplicadas permiten generar un Dataset coherente.

La validación se realizará **después de la detección de tipos y de la configuración de las transformaciones**, y antes de generar el Dataset definitivo.

El proceso será:

```text id="vld82a"
Definir tabla
      ↓
Previsualizar
      ↓
Detectar tipos
      ↓
Configurar columnas
      ↓
Aplicar transformaciones
      ↓
Validar
      ↓
Dataset
```

La validación no deberá modificar los datos por sí misma.

Su función será determinar:

* qué datos son válidos;
* qué datos presentan advertencias;
* qué datos contienen errores;
* si la configuración permite generar el Dataset.

---

## 9.2. Validación de la configuración

Antes de analizar los valores, el sistema deberá comprobar que la propia configuración de la tabla es coherente.

Se comprobarán, entre otros aspectos:

* que exista una fila de encabezados;
* que exista al menos una columna seleccionada;
* que el rango de columnas sea válido;
* que la fila inicial de datos sea válida;
* que la fila final no sea anterior a la inicial;
* que las columnas tengan nombres válidos;
* que no existan identificadores internos duplicados;
* que todas las columnas tengan un tipo utilizado;
* que las transformaciones configuradas sean válidas.

Si la configuración no es válida, el Dataset no podrá generarse.

---

## 9.3. Validación de nombres de columnas

Cada columna deberá disponer de un nombre identificable.

Se deberán detectar situaciones como:

```text id="9u2w1d"
[sin nombre]
```

o:

```text id="2x7q4m"
Nombre | Nombre
```

Los nombres duplicados podrán mantenerse visualmente en determinadas circunstancias, pero deberán disponer de identificadores internos diferentes.

La estrategia exacta de generación de estos identificadores se establecerá en la especificación técnica.

El objetivo es que cada columna pueda identificarse inequívocamente dentro del Dataset.

---

## 9.4. Validación de tipos

Cada columna deberá tener un tipo utilizado definido:

```text id="h8r3qa"
Text
Number
Date
Boolean
```

El tipo detectado por el sistema será únicamente una propuesta.

Por tanto:

```text id="f5m1vz"
Tipo detectado:
Text

Tipo utilizado:
Number
```

será una configuración válida siempre que los valores puedan normalizarse correctamente.

La validación comprobará que el tipo utilizado es compatible con los valores resultantes de las transformaciones.

---

## 9.5. Validación de valores numéricos

Cuando una columna esté configurada como `Number`, cada valor no vacío deberá poder convertirse correctamente en un número.

Ejemplo válido:

```text id="c6x9pw"
99.95
103.10
102.80
```

Ejemplo que deberá generar una incidencia:

```text id="y3n8kd"
99.95
103.10
abc
102.80
```

El sistema deberá identificar al menos:

* fila afectada;
* valor original;
* valor después de las transformaciones;
* tipo esperado;
* motivo del error.

No se sustituirá automáticamente `abc` por `0`, `null` u otro valor.

---

## 9.6. Validación de fechas

Cuando una columna esté configurada como `Date`, los valores deberán poder interpretarse como fechas válidas.

Se deberán detectar valores como:

```text id="u6q2me"
31/12/2026
01/01/2027
```

cuando sean compatibles con la configuración utilizada.

También deberán controlarse valores que no puedan convertirse correctamente:

```text id="w1p8rc"
31/12/2026
fecha desconocida
01/01/2027
```

El valor incorrecto deberá generar una incidencia y no convertirse silenciosamente en una fecha arbitraria.

---

## 9.7. Validación de valores booleanos

Una columna configurada como `Boolean` deberá producir valores inequívocos de verdadero o falso.

Ejemplo:

```text id="n4k7bs"
TRUE
FALSE
TRUE
```

Si mediante las transformaciones se han definido equivalencias:

```text id="a9f2de"
SI  → TRUE
NO  → FALSE
```

los valores resultantes serán válidos.

Un valor como:

```text id="m2v6qx"
QUIZÁS
```

deberá generar una incidencia si no existe una transformación que determine su significado.

---

## 9.8. Validación de texto

Las columnas de tipo `Text` serán las que requieran menos restricciones.

Cualquier valor que pueda representarse como texto podrá formar parte de una columna de este tipo.

No obstante, deberán seguir controlándose:

* valores inesperadamente vacíos cuando sean relevantes;
* errores procedentes de las transformaciones;
* caracteres o situaciones que impidan generar correctamente el Dataset.

Una columna `Text` no implica que cualquier contenido sea necesariamente correcto desde el punto de vista del negocio.

La primera versión se limitará a validar su integridad estructural.

---

## 9.9. Valores vacíos

Los valores vacíos deberán diferenciarse de los valores inválidos.

Por ejemplo:

```text id="k5r8yc"
PESO
----------------
103.20
[vacío]
102.80
```

no deberá considerarse automáticamente un error.

El sistema deberá registrar la cantidad de valores vacíos y permitir determinar posteriormente si una columna admite ausencia de información.

En la primera versión, la ausencia de un valor no será considerada automáticamente un error para todos los tipos.

---

## 9.10. Valores especiales

Los valores especiales previamente identificados deberán conservar su tratamiento durante la validación.

Ejemplo:

```text id="q8w3lm"
-
N/A
SIN DATOS
```

Si el usuario ha configurado:

```text id="s6p2vk"
"-" → vacío
```

el resultado podrá considerarse un valor ausente.

Si no existe dicha transformación y la columna es `Number`, el valor podrá generar una incidencia.

Esto permite diferenciar entre:

```text id="b7n4rx"
dato ausente configurado
```

y:

```text id="c3m9za"
dato incompatible con el tipo seleccionado
```

---

## 9.11. Validación de transformaciones

Cada transformación configurada deberá poder ejecutarse correctamente.

El sistema deberá detectar problemas como:

* transformación no válida;
* parámetros incompletos;
* orden incorrecto cuando provoque un resultado inválido;
* conversión imposible después de la transformación.

Ejemplo:

```text id="d4x8pq"
Valor:
"99.95 kg"

Transformación:
Eliminar "kg"

Resultado:
"99.95"

Conversión:
Number

Resultado:
Válido
```

Frente a:

```text id="e7m2kw"
Valor:
"abc kg"

Transformación:
Eliminar "kg"

Resultado:
"abc"

Conversión:
Number

Resultado:
Error
```

---

## 9.12. Errores, advertencias e información

La validación deberá diferenciar entre distintos niveles de resultado.

### Error

Impide generar el Dataset correctamente.

Ejemplo:

```text id="z3c6qn"
Columna Number
Valor "abc"
No se puede convertir a número.
```

### Advertencia

No impide necesariamente generar el Dataset, pero requiere la atención del usuario.

Ejemplo:

```text id="r5v9bx"
La columna contiene 12 valores especiales "-".
```

### Información

Describe una característica del Dataset sin indicar un problema.

Ejemplo:

```text id="p8k4sa"
La columna contiene 37 valores vacíos.
```

Esta separación permitirá que el usuario pueda distinguir rápidamente los problemas que debe solucionar de aquellos que simplemente debe conocer.

---

## 9.13. Resumen de validación

Al finalizar la validación, la aplicación deberá mostrar un resumen general.

Por ejemplo:

```text id="m4q7yc"
VALIDACIÓN

Columnas: 22
Filas: 1.178

Errores: 3
Advertencias: 7
Información: 15

Estado:
REVISIÓN NECESARIA
```

Cuando no existan errores bloqueantes:

```text id="v7x2nd"
VALIDACIÓN

Columnas: 22
Filas: 1.178

Errores: 0
Advertencias: 4
Información: 18

Estado:
LISTO PARA GENERAR DATASET
```

Las advertencias no deberán convertirse automáticamente en errores.

La decisión sobre determinadas situaciones podrá evolucionar posteriormente hacia reglas configurables.

---

## 9.14. Detalle de errores

El usuario deberá poder consultar el detalle de las incidencias detectadas.

Una incidencia deberá identificar, cuando sea posible:

* hoja;
* fila;
* columna;
* coordenada Excel;
* valor original;
* valor transformado;
* tipo esperado;
* motivo del problema.

Ejemplo:

```text id="j9s3wf"
Hoja:
Histórico

Celda:
E25

Columna:
PESO (KG)

Valor original:
"abc kg"

Valor transformado:
"abc"

Tipo esperado:
Number

Problema:
No se puede convertir el valor a Number.
```

Esto permitirá localizar directamente el problema en el Excel original.

---

## 9.15. Corrección y nueva validación

Cuando se detecten errores, el usuario deberá poder volver a la configuración correspondiente, modificar:

* el tipo;
* las transformaciones;
* el tratamiento de valores especiales;

y ejecutar nuevamente la validación.

El proceso será iterativo:

```text id="x6w1pz"
Validar
   ↓
Errores
   ↓
Modificar configuración
   ↓
Validar nuevamente
   ↓
Sin errores bloqueantes
   ↓
Dataset
```

No será necesario modificar el archivo Excel original para corregir problemas derivados de la interpretación o normalización.

---

## 9.16. Validación frente al Excel original

La validación deberá realizarse sobre los datos resultantes del proceso de normalización, pero manteniendo siempre la referencia al dato original.

Esto permitirá comparar:

```text id="t2k8qd"
Excel original
      ↓
Valor original
      ↓
Transformaciones
      ↓
Valor normalizado
      ↓
Validación
```

La información original no se perderá aunque el valor final sea diferente.

---

## 9.17. Condiciones para generar el Dataset

El Dataset podrá generarse cuando:

1. la definición de la tabla sea válida;
2. todas las columnas tengan una configuración válida;
3. todas las columnas tengan un tipo utilizado;
4. las transformaciones sean ejecutables;
5. no existan errores bloqueantes;
6. los valores incompatibles hayan sido corregidos, transformados o tratados según la configuración establecida.

Las advertencias no impedirán necesariamente la generación.

Por tanto:

```text id="k3p7mv"
Errores = 0
        +
Configuración válida
        ↓
Dataset disponible
```

---

## 9.18. Revalidación

La validación deberá poder ejecutarse tantas veces como sea necesario.

Cada modificación relevante de:

* rango;
* encabezados;
* tipos;
* transformaciones;
* tratamiento de valores especiales;

deberá provocar una nueva validación antes de considerar preparado el Dataset.

El objetivo es evitar que un Dataset generado anteriormente se considere automáticamente válido después de modificar su configuración.

---

## 9.19. Resultado de la fase de validación

La fase de validación producirá un estado que permita determinar si la información está preparada para convertirse en Dataset.

Conceptualmente:

```text id="s8q4wf"
Configuración
      ↓
Transformaciones
      ↓
Normalización
      ↓
Validación
      │
      ├── Error → Revisar
      │
      ├── Advertencia → Revisar / continuar
      │
      └── Correcto → Dataset
```

La validación no será una fase de limpieza automática.

Su función será garantizar que las decisiones tomadas durante la configuración producen datos compatibles con el modelo interno del sistema.

---

## 9.20. Principios de validación

La primera versión seguirá los siguientes principios:

1. **Validar antes de generar el Dataset.**
2. **No corregir silenciosamente los datos.**
3. **Conservar siempre el valor original.**
4. **Identificar los errores de forma localizable.**
5. **Diferenciar errores, advertencias e información.**
6. **No considerar automáticamente los valores vacíos como errores.**
7. **No considerar automáticamente los valores especiales como vacíos.**
8. **Validar el resultado de las transformaciones.**
9. **Permitir corregir la configuración y volver a validar.**
10. **No modificar el archivo Excel original.**
11. **Impedir la generación del Dataset cuando existan errores bloqueantes.**
12. **Mantener la trazabilidad entre el dato original y el dato normalizado.**

# 10. Generación del Dataset

## 10.1. Objetivo

El Dataset será la **representación interna normalizada de los datos procedentes del Excel**.

Su función será actuar como punto de separación entre:

```text id="d9w4kc"
FUENTE
Excel
  ↓
INTERPRETACIÓN
Detección + configuración
  ↓
PREPARACIÓN
Transformaciones + normalización
  ↓
VALIDACIÓN
Comprobación
  ↓
DATASET
  ↓
SALIDAS
Tablas + filtros + gráficos + informes
```

El Excel será considerado el origen de los datos, mientras que el Dataset será el formato que utilizará el resto de la aplicación.

De esta forma, las funcionalidades posteriores no tendrán que conocer las particularidades de cada archivo Excel.

---

## 10.2. El Dataset como contrato interno

El Dataset constituirá un contrato entre la fase de importación y las funcionalidades que consumirán los datos.

La parte de importación deberá encargarse de convertir:

```text id="x6c1va"
Excel
```

en:

```text id="n8r2km"
Dataset
```

Mientras que las funcionalidades posteriores trabajarán exclusivamente con:

```text id="j4p7qs"
Dataset
```

Por ejemplo:

```text id="s5m9xz"
Excel
 ├── Histórico
 ├── Objetivos
 ├── Alimentación y ejercicio
 └── Ejercicios fuerza
          ↓
     configuración
          ↓
       Dataset
          ↓
 ┌────────┼────────┐
 ↓        ↓        ↓
Tabla   Gráficos  Informes
```

Esto permitirá que una futura fuente CSV o Google Sheets pueda seguir el mismo camino:

```text id="q2v7bn"
CSV
 ↓
interpretación
 ↓
normalización
 ↓
Dataset
```

sin necesidad de desarrollar de nuevo la lógica de gráficos, filtros o informes.

---

## 10.3. Un Dataset por tabla configurada

En la primera versión, cada tabla configurada de una hoja generará un Dataset independiente.

Dado que V1 permite una única tabla por hoja:

```text id="w8m3fd"
1 Excel
   ↓
N hojas
   ↓
0..N tablas configuradas
   ↓
0..N Datasets
```

Una hoja que no tenga una tabla configurada no generará ningún Dataset.

Una hoja ignorada por el usuario tampoco generará Dataset.

---

## 10.4. El Dataset no representa el Excel completo

El Dataset no será una copia del archivo Excel.

No deberá contener necesariamente:

* formato visual;
* colores;
* bordes;
* tamaños de columnas;
* gráficos de Excel;
* fórmulas originales como mecanismo de cálculo;
* celdas situadas fuera de la tabla;
* elementos decorativos;
* leyendas;
* instrucciones existentes en otras partes de la hoja.

Su función será representar **los datos estructurados que el usuario ha seleccionado y configurado**.

Por ejemplo, en `Histórico`:

```text id="u5p2cx"
LEYENDA DE COLORES
...
fila 8 → encabezados
fila 9 → encabezados dinámicos
fila 10 → datos
...
```

La tabla configurada podría comenzar en:

```text id="y3r8qn"
A8:V1187
```

y el Dataset contendría únicamente la información correspondiente a la tabla y a la configuración establecida.

---

## 10.5. Estructura conceptual

El Dataset deberá contener como mínimo información sobre:

```text id="c9k4mw"
Dataset
├── identificación
├── origen
├── configuración
├── columnas
└── filas
```

Conceptualmente:

```text id="m7x2pd"
Dataset
│
├── id
├── nombre
├── origen
│   ├── archivo
│   └── hoja
│
├── tabla
│   ├── encabezados
│   ├── rango
│   └── filas
│
├── columnas
│   └── configuración de cada columna
│
└── datos
    └── valores normalizados
```

La estructura técnica concreta se definirá posteriormente.

---

## 10.6. Identificación del Dataset

Cada Dataset deberá disponer de una identificación propia.

El identificador deberá permitir diferenciarlo de otros Datasets generados a partir del mismo archivo.

Por ejemplo:

```text id="a4f8sq"
Dataset:
historico_peso

Origen:
CONTROL DE PESO Y TENSIÓN.xlsx

Hoja:
Histórico
```

La forma definitiva de generar los identificadores se establecerá en la especificación técnica.

---

## 10.7. Información de origen

El Dataset deberá conservar información suficiente para conocer de dónde proceden los datos.

Como mínimo:

```text id="z2n6vc"
Archivo:
CONTROL DE PESO Y TENSIÓN.xlsx

Hoja:
Histórico

Rango:
A8:V1187
```

También deberá conservarse la configuración utilizada para producirlo.

Esto permitirá mantener la trazabilidad:

```text id="r7m3kx"
Dataset
   ↓
Configuración
   ↓
Tabla
   ↓
Hoja
   ↓
Archivo original
```

---

## 10.8. Definición de columnas

Cada columna del Dataset deberá tener una definición explícita.

Conceptualmente:

```text id="p5w8jn"
Columna
├── id
├── nombre
├── nombre_original
├── tipo
├── configuración
└── transformaciones
```

Ejemplo:

```text id="e8c3mq"
id:
peso_kg

nombre:
Peso (kg)

nombre_original:
PESO (KG)

tipo:
Number
```

La separación entre nombre original y nombre interno permitirá trabajar con identificadores estables sin perder la referencia al Excel.

---

## 10.9. Valores normalizados

Los datos almacenados en el Dataset serán los valores resultantes del proceso de normalización.

Por ejemplo:

```text id="v6j2rk"
Excel:

"99.95 kg"

        ↓

Transformación:

Eliminar "kg"

        ↓

Normalización:

Number

        ↓

Dataset:

99.95
```

El Dataset no deberá necesitar conocer cómo se consiguió ese valor para poder utilizarlo.

Para las funcionalidades posteriores:

```text id="n4q7wx"
peso_kg = 99.95
```

será simplemente un valor numérico.

---

## 10.10. Conservación de la trazabilidad

Aunque el Dataset utilice valores normalizados, el sistema deberá conservar información suficiente para poder relacionarlos con el origen.

Conceptualmente:

```text id="j8m2vf"
Dataset
   │
   └── fila
        │
        ├── valor normalizado: 99.95
        └── origen: Histórico!E14
```

Esto será especialmente útil para:

* mostrar errores;
* investigar datos;
* revisar transformaciones;
* explicar cómo se obtuvo un valor;
* localizar el dato original dentro del Excel.

La cantidad exacta de metadatos que se conservará en el Dataset se concretará en la especificación técnica.

---

## 10.11. Filas del Dataset

Cada fila de la tabla configurada dará lugar a un registro del Dataset.

Ejemplo:

```text id="s3k8pd"
Fecha       | Peso   | IMC  | Grasa
--------------------------------------
...         | 103.10 | 31.5 | 0.311
```

internamente podrá representarse conceptualmente como:

```text id="q7f4xm"
{
    fecha: ...,
    peso: 103.10,
    imc: 31.5,
    grasa: 0.311
}
```

Los nombres utilizados internamente serán los identificadores definidos para las columnas.

---

## 10.12. Tipos del Dataset

Los valores del Dataset deberán respetar los tipos configurados.

Por ejemplo:

```text id="x8p3kr"
Fecha     → Date
Peso      → Number
IMC       → Number
Empleado  → Text
Activo    → Boolean
```

Esto permitirá que las funcionalidades posteriores puedan trabajar con los datos sin tener que volver a interpretar cadenas.

Por ejemplo, un filtro numérico podrá realizar:

```text id="c5m9vz"
peso > 100
```

sin tener que interpretar previamente:

```text id="r2q6bn"
"100 kg"
```

---

## 10.13. Ausencia de datos

El Dataset deberá poder representar la ausencia de información.

Por ejemplo:

```text id="p7w4mk"
Peso:
103.10

Peso:
null

Peso:
102.80
```

La ausencia de valor no deberá convertirse automáticamente en cero.

Esto es importante para posteriores:

* filtros;
* estadísticas;
* gráficos;
* cálculos;
* informes.

La representación técnica definitiva del valor ausente se establecerá posteriormente.

---

## 10.14. Dataset y fórmulas de Excel

Las fórmulas de Excel no serán el mecanismo de cálculo del Dataset.

Cuando una celda contenga una fórmula, el sistema utilizará su resultado calculado para generar el valor normalizado cuando corresponda.

Ejemplo:

```text id="f8m3qc"
Excel:

='Histórico'!E87 & "kg"

        ↓

Resultado:

"99.45kg"

        ↓

Transformaciones / normalización

        ↓

Dataset
```

La fórmula original podrá conservarse como metadato de origen, pero las funcionalidades posteriores trabajarán con el valor resultante.

Esto evita que los gráficos, filtros o informes dependan del motor de fórmulas de Excel.

---

## 10.15. Dataset independiente de la presentación

El Dataset no deberá contener información específica de cómo se visualizarán los datos.

Por ejemplo, el Dataset no decidirá:

* qué columnas aparecen en una tabla;
* qué gráfico utilizar;
* qué colores tendrá un gráfico;
* qué filtros estarán visibles;
* cómo se ordenará una pantalla;
* qué informe utilizará los datos.

Estas decisiones pertenecen a las capas posteriores de presentación y análisis.

La separación será:

```text id="m2q8vd"
Dataset
   ↓
Datos estructurados
   ↓
┌──────────┬──────────┬──────────┐
Tabla     Filtros    Gráficos
```

Esto permitirá reutilizar los mismos datos en diferentes salidas.

---

## 10.16. Dataset reutilizable

Una vez generado, el Dataset deberá poder ser utilizado por varias funcionalidades.

Por ejemplo, una misma columna:

```text id="v4x7ms"
Peso
```

podrá utilizarse posteriormente para:

```text id="b8n2kc"
Tabla
 ↓
Filtro
 ↓
Gráfico
 ↓
Informe
```

Sin necesidad de volver a leer o interpretar el Excel.

---

## 10.17. Generación del Dataset

La generación será el resultado de ejecutar la configuración validada sobre la tabla seleccionada.

Conceptualmente:

```text id="q5m8zr"
Excel
 ↓
Tabla seleccionada
 ↓
Valores originales
 ↓
Tipo utilizado
 ↓
Transformaciones
 ↓
Normalización
 ↓
Validación
 ↓
Dataset
```

La generación deberá utilizar exclusivamente una configuración que haya superado la validación.

---

## 10.18. Estado del Dataset

Un Dataset podrá encontrarse conceptualmente en diferentes estados:

```text id="j3p7vx"
Configuración
     ↓
En preparación
     ↓
Validado
     ↓
Generado
```

Si posteriormente cambia la configuración:

```text id="k8w2mq"
Dataset generado
       ↓
Cambio de configuración
       ↓
Dataset pendiente de regeneración
```

El sistema deberá evitar que un Dataset antiguo se considere automáticamente válido cuando su configuración de origen haya cambiado.

---

## 10.19. Regeneración

Cuando el usuario modifique:

* el rango de la tabla;
* los encabezados;
* los tipos;
* las transformaciones;
* el tratamiento de valores especiales;

será necesario volver a generar el Dataset.

El proceso será:

```text id="z6q3wn"
Configuración anterior
        ↓
Modificación
        ↓
Nueva validación
        ↓
Regeneración
        ↓
Nuevo Dataset
```

La estrategia exacta para conservar versiones anteriores se determinará posteriormente.

---

## 10.20. Dataset como punto de extensión futuro

El diseño del Dataset deberá evitar que su estructura dependa exclusivamente de Excel.

En el futuro, distintas fuentes podrán seguir un proceso equivalente:

```text id="w3m9kp"
Excel ───────────┐
                 │
CSV ─────────────┤
                 ├──→ Dataset ──→ Aplicación
                 │
Google Sheets ───┤
                 │
AD ──────────────┘
```

Cada fuente tendrá su propio mecanismo de extracción e interpretación, pero todas deberán poder producir una representación común.

Esto permitirá desarrollar posteriormente nuevas fuentes sin tener que reconstruir las funcionalidades de análisis y visualización.

---

## 10.21. Qué NO forma parte del Dataset inicial

El Dataset de la primera versión no tendrá como responsabilidad:

* almacenar el archivo Excel;
* sustituir una base de datos;
* gestionar usuarios;
* gestionar permisos;
* gestionar suscripciones;
* almacenar configuraciones de interfaz;
* definir gráficos;
* definir informes;
* ejecutar consultas complejas;
* relacionar automáticamente diferentes Datasets;
* realizar inteligencia artificial;
* realizar análisis semántico avanzado.

Estas funcionalidades podrán incorporarse posteriormente en otras capas del sistema.

---

## 10.22. Ejemplo completo

Tomando como ejemplo una columna del Excel:

```text id="r9c4mx"
PESO (KG)

" 99.95 kg "
" 103.10 kg "
-
" 102.80 kg "
```

la aplicación podría realizar:

```text id="f7w2kn"
1. Extracción
        ↓
2. Detección
        ↓
Tipo detectado: Text
        ↓
3. Configuración del usuario
        ↓
Tipo utilizado: Number
        ↓
4. Transformaciones
        ↓
trim
remove "kg"
"-" → vacío
        ↓
5. Normalización
        ↓
99.95
103.10
null
102.80
        ↓
6. Validación
        ↓
Correcto
        ↓
7. Dataset
```

El resultado podría conceptualizarse como:

```text id="m6q8vp"
Dataset: control_peso

Columnas:
- fecha: Date
- peso_kg: Number
- imc: Number
- grasa_corporal: Number
- agua_corporal: Number
- musculo_esqueletico: Number
- ...
```

Y las filas:

```text id="x4p9zc"
[
    {
        fecha: ...,
        peso_kg: 99.95,
        imc: 30.4,
        grasa_corporal: 0.286
    },
    {
        fecha: ...,
        peso_kg: 103.10,
        imc: 31.5,
        grasa_corporal: 0.311
    }
]
```

A partir de ese momento, las funcionalidades de la aplicación no necesitarán conocer que esos datos procedían originalmente de un archivo Excel.

---

## 10.23. Principios del Dataset

La primera versión seguirá los siguientes principios:

1. **El Excel es la fuente; el Dataset es la representación interna.**
2. **El Dataset se genera después de validar la configuración.**
3. **Los valores del Dataset están normalizados.**
4. **Cada columna tiene un tipo definido.**
5. **Los valores originales no se pierden durante el proceso.**
6. **Debe existir trazabilidad hasta el origen cuando sea necesario.**
7. **El Dataset es independiente de la presentación.**
8. **Una misma información puede alimentar varias funcionalidades.**
9. **El Dataset no debe depender exclusivamente de Excel.**
10. **Las futuras fuentes de datos deberán poder producir el mismo modelo conceptual.**
11. **Modificar la configuración implica volver a validar y generar el Dataset.**
12. **La estructura técnica deberá mantenerse lo suficientemente sencilla para el MVP.**

El Dataset será, por tanto, el punto central que conecta la importación de datos con las funcionalidades de análisis y visualización de la aplicación.

# 11. Consumo del Dataset: tablas, filtros y vistas

## 11.1. Objetivo

Una vez generado y validado el Dataset, la aplicación deberá permitir al usuario consultar y explorar sus datos.

El Dataset constituye la fuente de información para las funcionalidades de visualización, pero no determina por sí mismo cómo se muestran los datos.

La primera forma de consumo será mediante:

* tablas;
* filtros;
* ordenación;
* búsqueda;
* selección de columnas;
* vistas configurables.

El objetivo es permitir que el usuario pueda pasar de:

```text id="x8m4qk"
Dataset normalizado
```

a:

```text id="p6v2nz"
Vista de datos
```

sin tener que volver a trabajar directamente con el Excel.

---

## 11.2. Separación entre Dataset y vista

El Dataset contendrá los datos y su estructura.

La vista contendrá las instrucciones sobre cómo mostrar esos datos.

Conceptualmente:

```text id="k7r3mp"
Dataset
   │
   ├── columnas
   ├── tipos
   └── filas
        │
        ↓
      Vista
        │
        ├── columnas visibles
        ├── filtros
        ├── ordenación
        └── búsqueda
        │
        ↓
      Tabla
```

Esto permitirá crear diferentes vistas sobre el mismo Dataset sin duplicar los datos.

---

## 11.3. Vista inicial

Cuando un Dataset sea generado correctamente, la aplicación deberá poder mostrar una vista inicial de sus datos.

Esta vista deberá permitir como mínimo:

* visualizar las columnas;
* visualizar los registros;
* desplazarse por los datos;
* conocer el número de registros;
* conocer el número de columnas;
* ordenar los datos;
* filtrar los datos.

Ejemplo conceptual:

```text id="n5w8cq"
CONTROL DE PESO

[Fecha] [Peso] [IMC] [Grasa] [Agua] [...]

------------------------------------------------
24/08   103.10  31.5  31.1%   50.4%
25/08   102.80  31.4  30.9%   50.6%
26/08   102.40  31.3  30.7%   50.8%
------------------------------------------------

1.178 registros
22 columnas
```

La interfaz concreta se definirá posteriormente durante el diseño de la aplicación.

---

## 11.4. Tabla de datos

La tabla será una representación directa del Dataset.

Cada columna corresponderá a una columna del Dataset y cada fila a un registro.

Los valores deberán mostrarse respetando su tipo.

Por ejemplo:

```text id="q4c7xm"
Date      → fecha legible
Number    → número
Boolean   → verdadero/falso
Text      → texto
```

La tabla no deberá volver a interpretar los datos como si procedieran de Excel.

El Dataset ya contiene los valores normalizados.

---

## 11.5. Identificación de columnas

La tabla utilizará los nombres definidos en la configuración del Dataset.

Por ejemplo:

```text id="z8m3fp"
peso_kg
imc
grasa_corporal
agua_corporal
```

podrán mostrarse al usuario como:

```text id="j2q6vn"
Peso (kg)
IMC
Grasa corporal
Agua corporal
```

El identificador interno y el nombre mostrado deberán permanecer diferenciados.

Esto será especialmente importante para filtros, consultas y futuras configuraciones.

---

## 11.6. Ordenación

El usuario podrá ordenar los registros utilizando las columnas disponibles.

La ordenación deberá respetar el tipo de dato.

Por ejemplo:

### Number

```text id="w6p3kc"
98
100
102
110
```

y no:

```text id="x9r2mb"
100
102
110
98
```

como ocurriría al ordenar los valores como texto.

### Date

Las fechas deberán ordenarse cronológicamente.

### Text

Los textos se ordenarán alfabéticamente según las reglas establecidas por la aplicación.

### Boolean

Podrán ordenarse según un criterio definido por la aplicación.

La ordenación deberá ejecutarse sobre el Dataset o sobre una consulta de este, no sobre los valores visualizados como simples cadenas.

---

## 11.7. Ordenación ascendente y descendente

Cada columna compatible con ordenación podrá utilizar:

```text id="m7q4xs"
Ascendente
Descendente
```

Por ejemplo:

```text id="f3k8np"
Peso
↓
Mayor → menor
```

o:

```text id="v5m2qc"
Peso
↑
Menor → mayor
```

La interfaz deberá indicar visualmente qué columna está siendo utilizada para ordenar.

---

## 11.8. Filtros

Los filtros permitirán mostrar únicamente los registros que cumplan determinadas condiciones.

El tipo de filtro dependerá del tipo de columna.

### Number

Podrán plantearse condiciones como:

```text id="d8q3mk"
=
>
<
>=
<=
```

Ejemplo:

```text
Peso > 100
```

---

### Text

Podrán plantearse condiciones como:

```text id="c4n7vx"
contiene
empieza por
termina en
igual a
```

Ejemplo:

```text
Ejercicio contiene "caminar"
```

---

### Date

Podrán plantearse condiciones como:

```text id="p9w2kf"
antes de
después de
igual a
entre
```

---

### Boolean

Podrá seleccionarse:

```text id="x6m4qr"
TRUE
FALSE
```

---

## 11.9. Filtros combinados

El sistema deberá permitir combinar varios filtros.

Ejemplo:

```text id="n8q5mc"
Peso > 100
Y
IMC > 30
```

También podrán existir condiciones alternativas:

```text id="r4v7xp"
Departamento = "Administración"
O
Departamento = "Soporte"
```

La lógica completa de combinación de filtros podrá evolucionar posteriormente, pero la arquitectura deberá permitir construir condiciones compuestas.

---

## 11.10. Filtros sobre valores vacíos

El sistema deberá permitir diferenciar entre:

```text id="w3k8qn"
tiene valor
```

y:

```text id="m6p2vx"
está vacío
```

Esto será importante para columnas donde la ausencia de información sea significativa.

Por ejemplo:

```text id="z7c4kr"
Peso
→
Mostrar únicamente registros sin peso.
```

La aplicación no deberá confundir automáticamente:

```text id="v5m9px"
null
```

con:

```text id="x2q6nc"
0
```

ni con una cadena vacía si el Dataset los mantiene diferenciados.

---

## 11.11. Búsqueda

Además de los filtros estructurados, la aplicación podrá proporcionar una búsqueda general.

La búsqueda permitirá localizar rápidamente texto dentro del Dataset.

Ejemplo:

```text id="f8m3qw"
Buscar:
"caminar"
```

y mostrar los registros que contengan ese término en las columnas de texto correspondientes.

La búsqueda general será distinta de los filtros específicos.

```text id="j4p7kc"
Búsqueda
→ localizar información

Filtro
→ restringir registros según una condición
```

---

## 11.12. Selección de columnas

El usuario podrá seleccionar qué columnas quiere visualizar.

Por ejemplo, un Dataset puede contener 22 columnas, pero una vista concreta puede mostrar únicamente:

```text id="p6q8vn"
Fecha
Peso
IMC
Grasa corporal
Agua corporal
```

Las columnas no seleccionadas seguirán formando parte del Dataset.

Por tanto:

```text id="x3m7rf"
Dataset
22 columnas
      ↓
Vista
5 columnas visibles
```

La selección de columnas será una propiedad de la vista, no una modificación del Dataset.

---

## 11.13. Reordenación de columnas

La vista podrá permitir modificar el orden visual de las columnas.

Ejemplo:

```text id="k8v2mp"
Dataset:
Fecha | Peso | IMC | Grasa | Agua

Vista:
Fecha | Peso | Grasa | IMC | Agua
```

El orden interno del Dataset no tendrá que modificarse.

---

## 11.14. Paginación

Cuando el Dataset contenga un número elevado de registros, la aplicación no deberá intentar mostrar todos los registros simultáneamente.

Se utilizará paginación o un mecanismo equivalente.

Ejemplo:

```text id="m4q9xc"
Registros 1–50 de 1.178
```

con controles para navegar entre páginas.

La cantidad de registros mostrados por página podrá configurarse posteriormente.

El objetivo es mantener una interfaz manejable incluso con Datasets grandes.

---

## 11.15. Conteo de resultados

Cuando existan filtros activos, la aplicación deberá mostrar el número de registros que cumplen las condiciones.

Ejemplo:

```text id="c7n2pw"
Dataset:
1.178 registros

Filtro:
Peso > 100

Resultado:
143 registros
```

Esto permitirá conocer inmediatamente el efecto de los filtros.

---

## 11.16. Estado de la vista

La vista deberá poder representar el estado actual de consulta.

Conceptualmente:

```text id="v8m3qf"
Vista
├── Dataset
├── columnas visibles
├── filtros
├── búsqueda
├── ordenación
└── paginación
```

La modificación de cualquiera de estos elementos cambiará la forma en la que se muestran los datos, pero no modificará el Dataset.

---

## 11.17. Vistas configurables

El sistema deberá permitir, posteriormente, guardar una configuración de vista.

Por ejemplo:

```text id="q5x8mk"
Vista:
"Peso y evolución"

Dataset:
control_peso

Columnas:
Fecha
Peso
IMC

Orden:
Fecha descendente

Filtro:
Peso > 100
```

Otra vista podría utilizar exactamente el mismo Dataset:

```text id="n3r7vc"
Vista:
"Actividad"

Dataset:
control_peso

Columnas:
Fecha
Ejercicio

Filtro:
Ejercicio contiene "CAMINAR"
```

Ambas vistas utilizarían los mismos datos sin duplicarlos.

---

## 11.18. Vistas y cambios en el Dataset

Una vista dependerá del Dataset sobre el que fue creada.

Si el Dataset se regenera manteniendo las mismas columnas e identificadores, la vista podrá continuar funcionando.

Si se eliminan o modifican columnas utilizadas por la vista, será necesario detectar dicha incompatibilidad.

Ejemplo:

```text id="k6p2wr"
Vista:
Peso y evolución

Utiliza:
peso_kg
fecha
```

Si `peso_kg` desaparece del Dataset:

```text id="x4m8qn"
Vista
 ↓
Columna no disponible
 ↓
Revisión necesaria
```

La aplicación no deberá sustituir silenciosamente una columna por otra.

---

## 11.19. No modificación del Dataset

Las operaciones realizadas desde una vista no deberán modificar los datos originales del Dataset.

Por ejemplo:

```text id="j7q3mc"
Ordenar
Filtrar
Buscar
Ocultar columnas
Cambiar orden de columnas
```

son operaciones de visualización.

No deberán alterar:

```text id="z5m8kp"
Dataset
```

La modificación de los datos deberá realizarse únicamente mediante procesos explícitos de transformación o regeneración.

---

## 11.20. Preparación para futuras funcionalidades

El sistema deberá diseñarse de forma que una vista pueda servir posteriormente como base para otras funcionalidades.

Por ejemplo:

```text id="w4n9qx"
Dataset
   ↓
Vista
   ├── Tabla
   ├── Filtros
   ├── Gráficos
   └── Informe
```

Esto permitirá que una selección concreta de datos pueda reutilizarse posteriormente en diferentes formas de representación.

No obstante, los gráficos y los informes tendrán sus propias especificaciones y no formarán parte de esta fase.

---

## 11.21. Rendimiento

La interfaz deberá poder trabajar con Datasets que contengan más registros que los utilizados durante las primeras pruebas.

Por este motivo, la implementación deberá evitar cargar y renderizar innecesariamente todos los datos en pantalla cuando no sea necesario.

En el MVP se podrá trabajar con Datasets moderados y posteriormente optimizar:

* paginación;
* consultas;
* filtrado;
* ordenación;
* procesamiento en servidor;
* almacenamiento persistente.

No se establecerán todavía límites artificiales de tamaño sin haber realizado pruebas reales.

---

## 11.22. Estado vacío

La aplicación deberá gestionar correctamente situaciones en las que una consulta no produzca resultados.

Ejemplo:

```text id="q8m4vc"
Filtro:
Peso > 200

Resultado:
0 registros
```

La interfaz deberá indicar claramente que no existen registros que cumplan las condiciones actuales.

No deberá interpretarse como un error del Dataset.

---

## 11.23. Restablecimiento de filtros

El usuario deberá poder eliminar las condiciones aplicadas y volver a la vista completa.

Conceptualmente:

```text id="m5x9qk"
Vista filtrada
      ↓
Limpiar filtros
      ↓
Dataset completo
```

La operación no modificará el Dataset.

---

## 11.24. Resumen funcional

El consumo inicial del Dataset deberá permitir:

```text id="r7p3nx"
Dataset
   ↓
┌──────────────────────────┐
│          VISTA           │
├──────────────────────────┤
│ Columnas                 │
│ Ordenación               │
│ Filtros                  │
│ Búsqueda                 │
│ Paginación               │
└──────────────────────────┘
             ↓
           Tabla
```

El usuario podrá explorar los datos sin volver a interactuar directamente con el archivo Excel.

---

## 11.25. Principios de tablas, filtros y vistas

La primera versión seguirá los siguientes principios:

1. **El Dataset contiene los datos; la vista contiene la configuración de visualización.**
2. **Filtrar no modifica el Dataset.**
3. **Ordenar no modifica el Dataset.**
4. **Ocultar columnas no elimina columnas del Dataset.**
5. **Los filtros respetan el tipo de cada columna.**
6. **Los valores vacíos se diferencian de los valores cero.**
7. **La búsqueda general se diferencia de los filtros estructurados.**
8. **Las vistas podrán reutilizar el mismo Dataset.**
9. **Una vista podrá mostrar únicamente una selección de las columnas disponibles.**
10. **Los Datasets grandes deberán utilizar mecanismos adecuados de paginación o carga progresiva.**
11. **Los cambios en el Dataset deberán poder detectarse en las vistas dependientes.**
12. **La información del Dataset permanecerá separada de la presentación.**

La primera capa de consumo del sistema será, por tanto:

```text id="y8m3qc"
Dataset
   ↓
Vista
   ↓
Tabla + búsqueda + filtros + ordenación
```

Sobre esta base se construirán posteriormente las funcionalidades de **gráficos y análisis visual**, que constituirán una segunda forma de consumo del mismo Dataset.

# 12. Gráficos y visualizaciones

## 12.1. Objetivo

La fase de gráficos y visualizaciones tiene como objetivo permitir que el usuario represente visualmente la información contenida en un Dataset sin necesidad de modificar los datos originales ni programar manualmente cada gráfico.

Los gráficos no trabajarán directamente sobre el archivo Excel. Su fuente será siempre el **Dataset normalizado**, lo que garantiza que los valores utilizados en las visualizaciones ya han pasado por las fases de detección de tipos, configuración, transformación y validación.

El principio general será:

```text
Excel
 ↓
Dataset
 ↓
Vista / configuración
 ↓
Visualización
```

De esta forma, la visualización se considera una representación del Dataset y no una modificación del mismo.

El sistema deberá intentar determinar qué tipos de visualización son compatibles con las columnas disponibles y permitir al usuario configurar aquellos parámetros que sean necesarios.

---

## 12.2. Separación entre datos y visualización

Los gráficos no almacenarán los datos como una copia independiente del Dataset.

Una visualización estará formada conceptualmente por:

```text
Visualización
├── Dataset origen
├── tipo de gráfico
├── columna categoría/eje X
├── columna o columnas de valores
├── filtros
├── ordenación
├── configuración visual
└── opciones específicas
```

El Dataset continuará siendo la fuente de información.

Por ejemplo:

```text
Dataset
 ├── fecha
 ├── departamento
 ├── salario
 └── activo

        ↓

Gráfico
 ├── tipo: barras
 ├── categoría: departamento
 └── valor: salario
```

Modificar el gráfico no modificará el Dataset.

Del mismo modo, modificar posteriormente el Dataset no deberá modificar silenciosamente la configuración del gráfico. Si una columna utilizada por una visualización desaparece o cambia de tipo, el sistema deberá detectar la incompatibilidad y solicitar su revisión.

---

## 12.3. Tipos de gráficos iniciales

La primera versión deberá mantener un número limitado de tipos de gráficos, priorizando aquellos que puedan generarse de forma genérica a partir de los cuatro tipos de datos definidos en el Dataset.

Se contemplan inicialmente:

### Gráfico de barras

Adecuado para comparar valores entre categorías.

Ejemplos:

```text
Departamento → Número de empleados
Departamento → Salario medio
Producto → Unidades vendidas
```

Permitirá representar:

* categorías;
* cantidades;
* sumas;
* medias;
* recuentos;
* otras agregaciones compatibles.

Cuando existan muchas categorías, podrá utilizarse una orientación horizontal para mejorar la legibilidad.

---

### Gráfico de líneas

Adecuado principalmente para representar evolución o tendencias.

Ejemplos:

```text
Fecha → Peso
Fecha → Ventas
Fecha → Temperatura
Fecha → Número de incidencias
```

El eje temporal deberá respetar el tipo `Date` del Dataset y ordenar cronológicamente los registros.

No deberá tratar una fecha como un simple texto.

---

### Gráfico circular

Permitirá representar proporciones o distribución de un conjunto de categorías.

Ejemplos:

```text
Departamento → número de empleados
Estado → número de registros
Categoría → porcentaje del total
```

Su utilización deberá limitarse a situaciones donde las categorías representen partes de un mismo total.

No se utilizará como sustituto genérico de cualquier comparación entre categorías.

---

### Gráfico de dispersión

Permitirá representar la relación entre dos variables numéricas.

Ejemplos:

```text
Altura → Peso
Precio → Ventas
Edad → Salario
```

Cada registro del Dataset podrá representar un punto.

Este tipo de gráfico será especialmente útil para explorar relaciones entre variables, aunque no deberá interpretar automáticamente que la existencia de una correlación visual implique una relación causal.

---

## 12.4. Selección de columnas

La creación de un gráfico deberá partir de las columnas disponibles en el Dataset.

El sistema mostrará únicamente las configuraciones compatibles con el tipo de visualización seleccionada.

Por ejemplo:

```text
Tipo de gráfico: Línea

Eje X:
[ Fecha ▼ ]

Valor:
[ Peso ▼ ]
```

Mientras que un gráfico de dispersión podría mostrar:

```text
Tipo de gráfico: Dispersión

Eje X:
[ Altura ▼ ]

Eje Y:
[ Peso ▼ ]
```

La selección se basará en los tipos definidos en el Dataset.

De forma conceptual:

```text
Date + Number
        ↓
     Línea

Text + Number
        ↓
     Barras

Number + Number
        ↓
   Dispersión
```

Estas asociaciones serán propuestas por el sistema, pero la decisión final corresponderá al usuario.

---

## 12.5. Compatibilidad entre tipos y visualizaciones

El sistema deberá utilizar el tipo normalizado de las columnas para determinar qué configuraciones son razonables.

Ejemplos:

| Tipo de columna | Usos posibles                     |
| --------------- | --------------------------------- |
| Text            | Categoría, agrupación, filtro     |
| Number          | Valores, métricas, eje numérico   |
| Date            | Eje temporal, agrupación temporal |
| Boolean         | Categoría, recuento, filtro       |

Una columna `Text` no deberá utilizarse automáticamente como una medida numérica.

Por ejemplo:

```text
Peso = 99.95
```

podrá utilizarse como valor numérico.

Mientras que:

```text
Peso = "99.95 kg"
```

seguirá siendo texto hasta que el usuario configure las transformaciones necesarias para convertirlo en un número.

Esto refuerza la importancia de las fases anteriores de detección y normalización.

---

## 12.6. Agregaciones

Los gráficos no siempre representarán directamente cada fila del Dataset.

En determinados casos será necesario agrupar los datos.

Por ejemplo, si el Dataset contiene:

```text
Departamento | Empleado | Salario
Ventas       | Juan     | 1800
Ventas       | Ana      | 2100
Soporte      | Luis     | 1700
```

el usuario podría crear:

```text
Departamento → Salario medio
```

El sistema tendría que realizar conceptualmente:

```text
Ventas  → (1800 + 2100) / 2
Soporte → 1700
```

Las agregaciones iniciales podrán ser:

* `COUNT` — número de registros;
* `SUM` — suma;
* `AVG` — media;
* `MIN` — mínimo;
* `MAX` — máximo.

La agregación deberá ser explícita en la configuración de la visualización.

No se deberán realizar agregaciones silenciosas que puedan alterar la interpretación de los datos.

---

## 12.7. Agrupación

Cuando el usuario seleccione una columna categórica como eje o dimensión, los registros podrán agruparse por sus valores.

Ejemplo:

```text
Departamento
────────────
Ventas
Ventas
Soporte
Soporte
Soporte
Administración
```

Podrá convertirse en:

```text
Ventas          2
Soporte         3
Administración  1
```

Esta operación permitirá construir gráficos de recuentos y métricas agregadas.

Los valores vacíos deberán mantenerse diferenciados de los valores válidos.

Por ejemplo:

```text
Ventas
Soporte
NULL
```

no deberá convertirse automáticamente en:

```text
Ventas
Soporte
0
```

---

## 12.8. Filtros aplicados a las visualizaciones

Las visualizaciones deberán poder utilizar los filtros definidos en las vistas.

Conceptualmente:

```text
Dataset
   ↓
Filtros
   ↓
Datos resultantes
   ↓
Agregación
   ↓
Gráfico
```

Por ejemplo:

```text
Dataset completo
        ↓
Año = 2026
        ↓
Departamento = Ventas
        ↓
Salario medio por mes
        ↓
Gráfico de líneas
```

Esto permitirá que una misma fuente de datos pueda producir diferentes representaciones sin duplicar información.

Inicialmente se podrá reutilizar la lógica de filtros definida en la sección 11.

---

## 12.9. Ordenación

La ordenación de los datos representados deberá respetar el tipo de cada columna.

Por ejemplo:

### Número

```text
2
10
100
```

deberá ordenarse numéricamente y no como texto:

```text
10
100
2
```

### Fecha

```text
01/01/2026
15/01/2026
03/02/2026
```

deberá respetar el orden cronológico.

### Texto

Se utilizará ordenación alfabética.

La ordenación podrá ser relevante tanto para tablas como para gráficos.

En gráficos de evolución temporal, el orden cronológico tendrá prioridad sobre una ordenación alfabética de la representación textual de las fechas.

---

## 12.10. Configuración visual

La configuración visual deberá estar separada de la configuración de datos.

Podrán existir opciones como:

* título;
* descripción;
* etiquetas;
* mostrar u ocultar leyenda;
* mostrar valores;
* orientación del gráfico;
* formato de números;
* unidades;
* orden de categorías.

La configuración visual no deberá alterar los valores almacenados en el Dataset.

Por ejemplo:

```text
Dataset:
0.317
```

podrá mostrarse visualmente como:

```text
31,7 %
```

si la configuración correspondiente indica que ese valor debe presentarse como porcentaje.

Esta representación deberá distinguirse del valor normalizado almacenado.

---

## 12.11. Formato y unidades

El sistema deberá diferenciar entre:

```text
valor almacenado
valor normalizado
valor mostrado
```

Por ejemplo:

```text
Valor original:
"31,7 %"

Valor normalizado:
0.317

Valor mostrado:
31,7 %
```

La visualización podrá aplicar un formato de presentación sin modificar el valor del Dataset.

En V1 no será necesario crear tipos semánticos independientes para:

* porcentaje;
* moneda;
* distancia;
* peso;
* temperatura;
* velocidad.

Estos conceptos podrán incorporarse posteriormente mediante metadatos o tipos semánticos más avanzados.

Inicialmente el sistema deberá trabajar principalmente con `Number`, utilizando configuraciones de formato cuando sea necesario.

---

## 12.12. Visualizaciones recomendadas

El sistema podrá analizar las columnas del Dataset y mostrar sugerencias de visualización.

Por ejemplo, para:

```text
Fecha       Date
Peso        Number
```

podría sugerir:

```text
✓ Evolución del Peso
  Gráfico de líneas

✓ Peso por fecha
  Gráfico de líneas
```

Para:

```text
Departamento   Text
Salario        Number
```

podría sugerir:

```text
✓ Salario medio por departamento
  Gráfico de barras

✓ Número de empleados por departamento
  Gráfico de barras
```

Estas recomendaciones deberán entenderse como sugerencias técnicas basadas en la estructura de los datos y no como interpretaciones automáticas del significado empresarial del Dataset.

El usuario podrá ignorarlas y crear otra visualización compatible.

---

## 12.13. Configuración reproducible

Una visualización deberá poder representarse mediante una configuración estructurada.

Conceptualmente:

```text
Visualización
├── dataset_id
├── tipo
├── dimensiones
├── medidas
├── agregaciones
├── filtros
└── configuración visual
```

Por ejemplo:

```text
tipo: line
eje_x: fecha
valor: peso
agregación: none
filtro: ninguno
```

Esto permitirá que una visualización pueda reconstruirse sin almacenar una imagen estática del gráfico.

La aplicación deberá generar el gráfico a partir de su configuración y del Dataset.

Este planteamiento será especialmente importante para futuras funcionalidades como:

* guardar dashboards;
* compartir vistas;
* generar informes;
* exportar visualizaciones;
* reutilizar configuraciones;
* generar dashboards automáticamente.

---

## 12.14. Múltiples visualizaciones sobre un mismo Dataset

Un Dataset podrá tener múltiples visualizaciones.

Ejemplo:

```text
Dataset: empleados

 ├── Tabla completa
 ├── Empleados por departamento
 ├── Salario medio por departamento
 ├── Distribución de empleados activos
 └── Evolución de contrataciones
```

Todas las visualizaciones utilizarán el mismo Dataset.

No será necesario duplicar los datos para cada gráfico.

Esto permitirá que el futuro concepto de **Dashboard** sea una composición de diferentes vistas y visualizaciones:

```text
Dashboard
├── Dataset
├── Tabla
├── Gráfico 1
├── Gráfico 2
├── Gráfico 3
└── Filtros
```

Esta estructura será la base para una futura interfaz de dashboard configurable.

---

## 12.15. Actualización de las visualizaciones

Cuando una visualización se muestre, los datos deberán obtenerse del Dataset correspondiente.

Si el Dataset se regenera debido a cambios en:

* archivo Excel;
* rango;
* tipos;
* transformaciones;
* filtros estructurales;
* configuración de columnas;

las visualizaciones deberán comprobar que su configuración sigue siendo válida.

Si una columna utilizada por un gráfico deja de existir:

```text
Dataset
   ↓
columna "Salario" eliminada
   ↓
Visualización incompatible
```

el sistema deberá informar de la incidencia.

No deberá sustituir automáticamente la columna por otra aparentemente similar.

---

## 12.16. Estados de una visualización

Una visualización podrá encontrarse conceptualmente en diferentes estados:

```text
Configurada
     ↓
Validada
     ↓
Disponible
```

También podrá presentar estados de incidencia:

```text
Sin datos
Incompatible
Configuración incompleta
Error de agregación
```

Estos estados deberán diferenciarse de los errores del propio Dataset.

Por ejemplo:

```text
Dataset válido
+
Gráfico configurado con una columna inexistente
=
Dataset correcto + visualización inválida
```

La responsabilidad de cada componente deberá mantenerse separada.

---

## 12.17. Límites iniciales

Para mantener el MVP controlado, la primera versión no incluirá:

* gráficos 3D;
* mapas;
* gráficos extremadamente especializados;
* análisis estadístico avanzado;
* predicciones;
* detección automática de tendencias;
* inteligencia artificial para interpretar gráficos;
* correlaciones automáticas con conclusiones;
* dashboards colaborativos;
* edición simultánea;
* exportación avanzada a múltiples formatos.

Estas funcionalidades podrán estudiarse posteriormente en función de las necesidades reales del producto.

---

## 12.18. Relación con el concepto de Dashboard

La introducción de visualizaciones permitirá evolucionar progresivamente desde una aplicación de importación de Excel hacia un sistema de dashboards.

La arquitectura conceptual será:

```text
Excel
 ↓
Extracción
 ↓
Interpretación
 ↓
Normalización
 ↓
Dataset
 ↓
┌──────────────────────────────┐
│                              │
├── Tabla                      │
├── Filtros                    │
├── Gráfico                    │
├── Gráfico                    │
├── Indicador                  │
└── Informe                    │
                               │
└──────────────────────────────┘
```

El Dashboard no será el origen de los datos.

Será una **capa de presentación construida sobre el Dataset**.

Esta separación permitirá que una misma arquitectura pueda utilizar posteriormente otros orígenes:

```text
Excel ───────┐
CSV ─────────┤
Google Sheets ┤
AD ──────────┤
Base de datos ┤
API ──────────┘
       ↓
    Dataset
       ↓
 Dashboard
```

Por tanto, el desarrollo de los gráficos en este proyecto no deberá estar acoplado específicamente a Excel.

---

## 12.19. Principios de diseño

La funcionalidad de gráficos deberá respetar los siguientes principios:

1. **El Dataset es la fuente de verdad.**
2. **Los gráficos no modifican los datos.**
3. **Una visualización es una configuración, no una copia de datos.**
4. **La selección de columnas debe respetar sus tipos.**
5. **Las agregaciones deben ser explícitas.**
6. **Los filtros deben poder reutilizarse.**
7. **Los valores vacíos deben diferenciarse de cero.**
8. **El formato visual no debe modificar el valor normalizado.**
9. **Las visualizaciones deben poder reconstruirse a partir de su configuración.**
10. **Una visualización inválida no debe invalidar automáticamente el Dataset.**
11. **El sistema podrá sugerir gráficos, pero el usuario mantiene el control.**
12. **La lógica de visualización debe ser independiente del origen Excel.**

---

## 12.20. Resultado esperado

Al finalizar esta fase, el usuario deberá poder partir de un Dataset ya validado y construir visualizaciones seleccionando:

```text
Dataset
   ↓
Tipo de gráfico
   ↓
Columnas
   ↓
Agrupación / agregación
   ↓
Filtros
   ↓
Configuración visual
   ↓
Visualización
```

El resultado será un sistema capaz de convertir datos estructurados en representaciones visuales reutilizables.

La arquitectura resultante permitirá que Excel Dashboard evolucione desde:

```text
"Subir un Excel y verlo"
```

hacia:

```text
"Importar datos → estructurarlos → analizarlos → visualizarlos → construir dashboards"
```

sin necesidad de cambiar el concepto fundamental del sistema: **el Dataset actúa como contrato entre la entrada de datos y las diferentes formas de consumirlos.**

# 13. Cuadros de mando y composición de dashboards

## 13.1. Objetivo

El cuadro de mando constituye una capa de presentación que permite combinar diferentes elementos de información procedentes de uno o varios Datasets en una única interfaz visual.

Su objetivo es facilitar una visión resumida e interactiva de la información, permitiendo al usuario combinar:

* indicadores;
* tablas;
* gráficos;
* filtros;
* búsquedas;
* textos descriptivos;
* otros componentes de visualización que puedan incorporarse posteriormente.

El cuadro de mando no almacenará una copia independiente de los datos.

Su función será definir **qué información mostrar y cómo organizarla visualmente**, utilizando como fuente los Datasets y vistas existentes.

La relación conceptual será:

```text
Fuente
  ↓
Dataset
  ↓
Vista / consultas
  ↓
Componentes
  ↓
Dashboard
```

---

## 13.2. Dashboard como composición

Un Dashboard estará formado por una colección de componentes independientes.

Conceptualmente:

```text
Dashboard
├── configuración general
├── filtros
├── componente KPI
├── componente gráfico
├── componente gráfico
├── componente tabla
└── componente texto
```

Cada componente tendrá su propia configuración y referencia al Dataset o vista que utiliza.

Por ejemplo:

```text
Dashboard: Ventas 2026

├── KPI → Ventas totales
├── KPI → Número de pedidos
├── KPI → Ticket medio
├── Gráfico → Ventas por mes
├── Gráfico → Ventas por categoría
└── Tabla → Últimos pedidos
```

Esta estructura permitirá añadir, eliminar o modificar componentes sin alterar el Dataset.

---

## 13.3. Tipos de componentes iniciales

La primera versión podrá contemplar los siguientes componentes:

### Indicador o KPI

Representará un valor resumido.

Ejemplos:

```text
VENTAS TOTALES
125.430 €

PEDIDOS
1.248

TICKET MEDIO
100,50 €
```

Un KPI podrá utilizar operaciones como:

* recuento;
* suma;
* media;
* mínimo;
* máximo.

Podrá mostrar opcionalmente información adicional, como:

* periodo;
* unidad;
* valor anterior;
* diferencia;
* porcentaje de variación.

Las comparaciones avanzadas podrán incorporarse posteriormente.

---

### Tabla

Permitirá mostrar registros del Dataset.

Podrá reutilizar las funcionalidades definidas en la sección 11:

* selección de columnas;
* ordenación;
* filtros;
* búsqueda;
* paginación.

La tabla del Dashboard será una representación del Dataset y no una copia independiente.

---

### Gráfico

Utilizará el sistema de visualizaciones definido en la sección 12.

Podrá incorporar:

* barras;
* líneas;
* circular;
* dispersión;
* otros tipos posteriormente.

Cada gráfico tendrá su propia configuración.

---

### Texto

Permitirá incorporar elementos informativos al Dashboard.

Ejemplos:

```text
Resumen mensual de ventas.

Datos actualizados el 24/09/2026.

El análisis incluye únicamente pedidos confirmados.
```

El componente de texto no dependerá necesariamente de un Dataset.

---

## 13.4. Diseño y distribución

Los componentes deberán poder organizarse dentro de una estructura visual.

Conceptualmente:

```text
┌──────────────────────────────────────────────┐
│              TÍTULO DEL DASHBOARD             │
├──────────────┬──────────────┬────────────────┤
│ KPI          │ KPI          │ KPI            │
├──────────────┴──────────────┴────────────────┤
│                                              │
│                 GRÁFICO                      │
│                                              │
├───────────────────────────┬──────────────────┤
│ GRÁFICO                   │ TABLA            │
│                           │                  │
└───────────────────────────┴──────────────────┘
```

La posición y dimensiones de cada componente formarán parte de la configuración del Dashboard.

Inicialmente no será necesario implementar un sistema de diseño extremadamente complejo.

Podrá utilizarse una estructura basada en filas, columnas o una cuadrícula.

---

## 13.5. Configuración independiente del contenido

La posición visual de un componente deberá estar separada de su configuración de datos.

Por ejemplo:

```text
Componente
├── tipo: gráfico
├── dataset: ventas
├── configuración de datos
└── posición:
    ├── fila
    ├── columna
    ├── ancho
    └── alto
```

Esto permitirá modificar el diseño sin tener que volver a configurar el gráfico.

Del mismo modo, cambiar la consulta o el filtro de un componente no deberá modificar su posición dentro del Dashboard.

---

## 13.6. Filtros globales

Un Dashboard podrá disponer de filtros que afecten a varios componentes simultáneamente.

Por ejemplo:

```text
Año: [2026]
Departamento: [Ventas]
Mes: [Todos]
```

Estos filtros podrán aplicarse a diferentes componentes que utilicen columnas compatibles.

Conceptualmente:

```text
                 Filtro global
                     ↓
          ┌──────────┼──────────┐
          ↓          ↓          ↓
         KPI       Gráfico     Tabla
          ↓          ↓          ↓
             Datos filtrados
```

Esto permitirá que el usuario modifique el contexto del Dashboard sin tener que cambiar individualmente cada componente.

---

## 13.7. Filtros específicos de componente

Además de los filtros globales, cada componente podrá tener filtros propios.

Ejemplo:

```text
Dashboard
│
├── Filtro global: Año = 2026
│
├── KPI
│   └── Departamento = Ventas
│
├── Gráfico
│   └── Categoría = Electrónica
│
└── Tabla
    └── Estado = Pendiente
```

De esta forma se podrá combinar:

```text
Filtro global
+
Filtro específico
```

sin modificar el Dataset.

---

## 13.8. Relación entre filtros y componentes

No todos los filtros serán necesariamente aplicables a todos los componentes.

El sistema deberá comprobar la compatibilidad.

Por ejemplo, un filtro:

```text
Departamento = Ventas
```

podrá aplicarse a componentes que utilicen un Dataset que contenga la columna `Departamento`.

Si un componente utiliza otro Dataset sin dicha columna, deberá informarse de que ese componente no puede aplicar el filtro.

No se deberán realizar sustituciones automáticas de columnas.

---

## 13.9. Datasets múltiples

Inicialmente se podrá trabajar principalmente con un Dataset por Dashboard.

Sin embargo, la arquitectura deberá permitir posteriormente utilizar varios Datasets.

Por ejemplo:

```text
Dashboard empresarial

Dataset ventas
Dataset empleados
Dataset inventario
```

Esto permitirá construir dashboards más completos.

La utilización simultánea de varios Datasets deberá introducirse de forma controlada porque puede requerir mecanismos adicionales de relación entre datos.

Por tanto, en la primera implementación podrá limitarse el Dashboard a un Dataset principal, dejando preparada la configuración para una futura ampliación.

---

## 13.10. Relaciones entre Datasets

La posibilidad de utilizar varios Datasets plantea la necesidad de establecer relaciones entre ellos.

Ejemplo:

```text
Dataset ventas
    │
    └── producto_id
             │
             ↓
Dataset productos
    │
    └── producto_id
```

En versiones futuras podrían definirse relaciones o combinaciones entre Datasets.

Sin embargo, esta funcionalidad no formará parte del MVP inicial.

La prioridad será comprobar primero que el modelo:

```text
Dataset → componentes → Dashboard
```

funciona correctamente con un único Dataset.

---

## 13.11. Indicadores calculados

Los KPIs podrán utilizar operaciones básicas sobre los datos.

Ejemplos:

```text
COUNT(registros)
SUM(ventas)
AVG(salario)
MIN(fecha)
MAX(fecha)
```

Posteriormente podrán incorporarse cálculos más avanzados.

También podrá existir un sistema de métricas calculadas que permita definir expresiones sobre columnas.

Ejemplo conceptual:

```text
Margen = Ventas - Costes
```

Esta funcionalidad deberá desarrollarse posteriormente, ya que introduce una capa de cálculo distinta de la normalización del Dataset.

---

## 13.12. Interactividad

El Dashboard podrá incorporar interacciones entre componentes.

Por ejemplo:

```text
Usuario selecciona "Ventas"
             ↓
Filtro / selección
             ↓
Actualización
             ↓
KPI + gráfico + tabla
```

Esto permitirá que los dashboards sean interactivos y no únicamente páginas estáticas.

En la primera versión se priorizarán los filtros globales y la actualización de componentes.

Interacciones más complejas, como seleccionar una barra de un gráfico para filtrar automáticamente el resto del Dashboard, podrán incorporarse posteriormente.

---

## 13.13. Estado del Dashboard

El Dashboard deberá mantener su configuración independientemente del Dataset.

Conceptualmente:

```text
Dashboard
├── identificación
├── nombre
├── Dataset(s)
├── filtros
├── componentes
├── distribución
└── configuración visual
```

Los componentes deberán conservar sus configuraciones.

Si el Dataset cambia, el Dashboard deberá comprobar que sus componentes siguen siendo compatibles.

Por ejemplo:

```text
Dashboard
   ↓
Gráfico utiliza "Ventas"
   ↓
Dataset regenerado
   ↓
"Ventas" ya no existe
   ↓
Componente incompatible
```

El sistema deberá marcar el componente como pendiente de revisión.

No deberá sustituirlo automáticamente por otra columna.

---

## 13.14. Estados del Dashboard

Un Dashboard podrá encontrarse conceptualmente en estados como:

```text
Borrador
   ↓
Configurado
   ↓
Validado
   ↓
Publicado / Disponible
```

También podrán existir incidencias:

```text
Componente incompatible
Dataset inexistente
Filtro inválido
Configuración incompleta
```

Un error de un componente no deberá ocultar necesariamente el resto del Dashboard.

Por ejemplo:

```text
┌─────────────────────────────────┐
│ KPI              │ KPI          │
├──────────────────┼──────────────┤
│ GRÁFICO VÁLIDO   │ ⚠ ERROR      │
│                  │ Componente   │
│                  │ incompatible │
├──────────────────┴──────────────┤
│ TABLA VÁLIDA                    │
└─────────────────────────────────┘
```

Esto permitirá identificar y corregir problemas de forma localizada.

---

## 13.15. Guardado y reutilización

La configuración del Dashboard deberá poder guardarse de forma estructurada.

Conceptualmente:

```text
Dashboard
{
    nombre,
    dataset,
    filtros,
    componentes,
    layout,
    configuración
}
```

El objetivo será que el Dashboard pueda reconstruirse posteriormente sin almacenar simplemente una captura de pantalla.

Esto permitirá:

* editar dashboards;
* duplicarlos;
* crear versiones;
* reutilizarlos;
* exportarlos;
* compartirlos posteriormente.

---

## 13.16. Dashboard y Vista

Debe mantenerse la diferencia conceptual entre una Vista y un Dashboard.

Una **Vista** representa una forma concreta de consultar o visualizar un Dataset.

Un **Dashboard** combina diferentes componentes y vistas para presentar información de forma conjunta.

Conceptualmente:

```text
Dataset
   ↓
Vistas
   ├── Vista tabla
   ├── Vista filtrada
   └── Vista agregada
          ↓
      Dashboard
          ├── KPI
          ├── gráfico
          ├── gráfico
          └── tabla
```

Una vista podrá utilizarse de forma independiente o formar parte de un Dashboard.

---

## 13.17. Dashboard y presentación

El Dashboard deberá representar información de forma clara y comprensible.

Se deberá evitar que la interfaz dependa excesivamente de elementos decorativos.

La prioridad será:

1. información;
2. legibilidad;
3. jerarquía visual;
4. interacción;
5. estética.

Los elementos visuales deberán facilitar la interpretación de los datos y no ocultar información relevante.

---

## 13.18. Límites iniciales

Para mantener el MVP controlado, inicialmente no se incluirán:

* colaboración simultánea;
* dashboards públicos;
* permisos avanzados;
* comentarios;
* edición multiusuario;
* relaciones complejas entre Datasets;
* lenguaje avanzado de expresiones;
* inteligencia artificial para diseñar dashboards;
* diseños completamente libres tipo software de diseño gráfico;
* animaciones avanzadas.

Estas capacidades podrán evaluarse posteriormente.

---

## 13.19. Evolución hacia una plataforma de Business Intelligence

La introducción de Dashboards permite definir una arquitectura mucho más amplia:

```text
                 FUENTES
                    ↓
          ┌───────────────────┐
          │   IMPORTADORES     │
          └─────────┬─────────┘
                    ↓
                 DATASET
                    ↓
          ┌───────────────────┐
          │       VISTAS      │
          └─────────┬─────────┘
                    ↓
       ┌────────────┼────────────┐
       ↓            ↓            ↓
     TABLAS      GRÁFICOS      KPIs
       └────────────┼────────────┘
                    ↓
               DASHBOARD
                    ↓
                INFORMES
```

Este diseño permite que la aplicación evolucione progresivamente hacia una herramienta de análisis y presentación de datos sin tener que convertir desde el principio el proyecto en una plataforma empresarial completa.

---

## 13.20. Principios de diseño

El sistema de Dashboards deberá respetar los siguientes principios:

1. **El Dashboard no es el Dataset.**
2. **Los componentes no deben duplicar los datos.**
3. **Los componentes deben ser configurables de forma independiente.**
4. **Los filtros globales deben poder afectar a varios componentes.**
5. **Los filtros específicos deben permanecer separados de los globales.**
6. **Las incompatibilidades deben detectarse explícitamente.**
7. **No deben producirse sustituciones silenciosas de columnas.**
8. **El diseño visual debe estar separado de la configuración de datos.**
9. **La configuración debe poder guardarse y reconstruirse.**
10. **El Dashboard debe poder evolucionar sin modificar el Dataset.**
11. **La arquitectura debe permitir incorporar nuevos componentes.**
12. **La primera implementación debe priorizar un Dataset y una estructura sencilla.**

---

## 13.21. Resultado esperado

Al finalizar esta fase, el sistema deberá poder transformar un Dataset en un espacio de análisis visual compuesto por diferentes elementos:

```text
Dataset
   ↓
Dashboard
   ├── KPIs
   ├── tablas
   ├── gráficos
   ├── filtros
   └── información contextual
```

El usuario podrá utilizar estos elementos conjuntamente para construir una visión personalizada de sus datos.

El concepto de Dashboard será, por tanto, una **capa de composición sobre las funcionalidades de Dataset, vistas y visualizaciones**, y no una funcionalidad aislada.

La arquitectura resultante permitirá posteriormente añadir informes, exportación, compartición y otras formas de distribución de la información sin modificar el núcleo de procesamiento de datos.

# 14. Informes y exportación

## 14.1. Objetivo

La funcionalidad de informes y exportación permitirá transformar la información contenida en los Datasets, vistas y Dashboards en resultados que puedan ser consultados, almacenados, imprimidos o compartidos fuera de la aplicación.

Esta fase deberá contemplar dos conceptos relacionados pero diferentes:

* **Exportación:** obtener los datos o una representación concreta en un formato externo.
* **Informe:** construir un documento estructurado que combina información, métricas, tablas y visualizaciones con un propósito concreto.

El principio general será:

```text id="q4n7va"
Dataset
   ↓
Vistas / Visualizaciones / Dashboard
   ↓
┌───────────────────────┐
│                       │
├── Exportación de datos
├── Exportación de vista
├── Exportación de gráfico
└── Generación de informe
```

La información exportada deberá proceder de los datos y configuraciones existentes, evitando crear una segunda fuente de información independiente.

---

## 14.2. Diferencia entre exportación e informe

La aplicación deberá diferenciar claramente ambos conceptos.

### Exportación

Su finalidad será obtener información en un formato determinado.

Ejemplos:

```text id="5wm4sl"
Dataset → CSV
Tabla → CSV
Dataset → Excel
Gráfico → imagen
Dashboard → PDF
```

La exportación estará orientada principalmente a transportar o distribuir información.

### Informe

Su finalidad será presentar información de forma estructurada y contextualizada.

Ejemplo:

```text id="8f8y1u"
INFORME MENSUAL DE VENTAS

Periodo: septiembre 2026

Ventas totales: 125.430 €
Pedidos: 1.248
Ticket medio: 100,50 €

[Evolución mensual]

[Ventas por departamento]

[Tabla resumen]

Observaciones
...
```

Un informe puede utilizar diferentes fuentes de información, mientras que una exportación puede limitarse a convertir una única vista o conjunto de datos.

---

## 14.3. Exportación del Dataset

El Dataset podrá exportarse en formatos estructurados.

Inicialmente se contempla:

* CSV;
* Excel;
* JSON.

La exportación deberá utilizar los valores normalizados del Dataset y no los valores originales del Excel.

Por ejemplo:

```text id="s3x5s7"
Excel original:
" 99,95 kg "

Dataset:
99.95

Exportación:
99.95
```

Esto permitirá que el archivo exportado represente los datos ya procesados por la aplicación.

La exportación no deberá modificar el Dataset.

---

## 14.4. Exportación de datos originales

Aunque el Dataset será la fuente principal para las exportaciones, deberá mantenerse conceptualmente la posibilidad de acceder al archivo original.

Por tanto, deberá distinguirse entre:

```text id="s1slk9"
Archivo original
       ↓
Datos originales
       ↓
Dataset normalizado
       ↓
Exportaciones
```

El sistema no deberá presentar una exportación normalizada como si fuera una copia del Excel original.

La trazabilidad deberá permitir conocer, cuando sea necesario, de qué archivo, hoja y zona de origen proceden los datos.

---

## 14.5. Exportación de tablas

Una vista de tipo tabla podrá exportarse respetando su configuración.

Por ejemplo:

```text id="kzj0nr"
Dataset completo
        ↓
Filtro: Año = 2026
        ↓
Columnas visibles:
Fecha
Departamento
Ventas
        ↓
Exportar CSV
```

La exportación podrá realizarse sobre:

* todos los registros;
* únicamente los registros filtrados;
* las columnas seleccionadas;
* la ordenación aplicada.

Deberá quedar claro para el usuario si está exportando:

```text
Dataset completo
```

o:

```text
Resultado actual de la vista
```

para evitar confusiones.

---

## 14.6. Exportación de gráficos

Las visualizaciones podrán exportarse independientemente del Dataset.

Entre los formatos posibles se contemplan:

* imagen;
* PDF;
* otros formatos gráficos en futuras versiones.

La exportación deberá representar el gráfico tal como está configurado en la aplicación.

Por ejemplo:

```text id="xkj1d0"
Dataset
 ↓
Filtro
 ↓
Agregación
 ↓
Gráfico
 ↓
Exportar
```

El archivo generado deberá corresponder a la visualización resultante y no a los datos sin procesar.

---

## 14.7. Exportación de Dashboards

Un Dashboard podrá exportarse como una representación estática.

El formato más relevante será inicialmente PDF.

Conceptualmente:

```text id="w4b19p"
Dashboard interactivo
        ↓
Captura estructurada
        ↓
PDF
```

El PDF deberá conservar, en la medida de lo posible:

* título;
* componentes;
* tablas;
* gráficos;
* KPIs;
* filtros aplicados;
* información contextual.

La versión exportada será estática y, por tanto, no conservará la interactividad propia del Dashboard.

La aplicación deberá distinguir claramente entre:

```text
Dashboard
→ interactivo

PDF del Dashboard
→ estático
```

---

## 14.8. Generación de informes

El sistema deberá permitir crear informes utilizando diferentes componentes de información.

Conceptualmente:

```text id="3w7h7q"
Informe
├── cabecera
├── título
├── periodo
├── descripción
├── indicadores
├── tablas
├── gráficos
├── observaciones
└── pie
```

Los componentes podrán proceder de Datasets o Dashboards existentes.

Un informe no deberá requerir que toda la información proceda necesariamente de un único tipo de componente.

---

## 14.9. Estructura de un informe

Una estructura inicial podrá ser:

```text id="6tv9fr"
INFORME
│
├── Información general
│   ├── título
│   ├── descripción
│   ├── fecha
│   └── periodo
│
├── Resumen
│   ├── KPI
│   ├── KPI
│   └── KPI
│
├── Análisis
│   ├── gráfico
│   ├── gráfico
│   └── tabla
│
└── Información adicional
    ├── notas
    └── origen de los datos
```

Esta estructura deberá ser configurable.

---

## 14.10. Plantillas de informes

Para evitar tener que construir manualmente cada informe desde cero, el sistema podrá permitir definir plantillas.

Conceptualmente:

```text id="f9fhkp"
Plantilla de informe
├── estructura
├── componentes
├── posiciones
├── estilos
└── configuración
```

Posteriormente, una plantilla podrá utilizarse con diferentes Datasets compatibles.

Ejemplo:

```text id="9d0x0s"
Plantilla:
"Informe mensual de ventas"

        ↓

Dataset enero
        ↓
Informe enero

Dataset febrero
        ↓
Informe febrero

Dataset marzo
        ↓
Informe marzo
```

Esto permitirá evolucionar posteriormente hacia informes periódicos automatizados.

---

## 14.11. Periodos y fechas

Los informes podrán incorporar información temporal.

Por ejemplo:

```text id="4ty0ph"
Periodo analizado:
01/09/2026 – 30/09/2026
```

Cuando el Dataset disponga de una columna de tipo `Date`, el sistema podrá utilizarla para definir periodos.

Los filtros temporales deberán utilizar fechas reales del Dataset y no comparaciones de texto.

Podrán contemplarse posteriormente periodos:

* diarios;
* semanales;
* mensuales;
* trimestrales;
* anuales;
* personalizados.

---

## 14.12. Información de origen y trazabilidad

Los informes deberán poder incluir información sobre el origen de los datos.

Ejemplo:

```text id="v3x9mz"
Fuente:
CONTROL DE PESO Y TENSIÓN.xlsx

Hoja:
Histórico

Rango:
A8:V1187

Generado:
24/09/2026
```

Esta información permitirá que el resultado sea más auditable y comprensible.

La trazabilidad será especialmente importante cuando los informes se utilicen fuera de la aplicación.

---

## 14.13. Estado de los datos al generar un informe

Un informe deberá utilizar información procedente de un Dataset válido.

El sistema no deberá generar silenciosamente un informe a partir de una configuración incompleta.

El flujo será:

```text id="0b0g6m"
Dataset
   ↓
Validación
   ↓
Vista / Dashboard
   ↓
Informe
   ↓
Exportación
```

Si un componente del informe presenta una incompatibilidad, deberá detectarse antes de generar el resultado final.

---

## 14.14. Valores y cálculos

Los informes deberán distinguir entre:

```text id="qvkl5q"
Valor del Dataset
        ↓
Cálculo
        ↓
Valor mostrado
```

Por ejemplo:

```text
Dataset:
125430.25

Informe:
125.430,25 €
```

El formato utilizado en el informe no deberá modificar el valor original del Dataset.

Los cálculos utilizados deberán ser reproducibles y, cuando sea relevante, quedar identificados en la configuración del informe.

---

## 14.15. Informes con filtros

Un informe podrá representar el resultado de una vista filtrada.

Por ejemplo:

```text id="3qujpj"
Dataset
 ↓
Año = 2026
 ↓
Departamento = Ventas
 ↓
Informe
```

El informe deberá poder indicar qué filtros fueron aplicados.

Ejemplo:

```text id="a8c5hl"
Filtros aplicados

Año: 2026
Departamento: Ventas
Estado: Activo
```

Esto permitirá conocer el contexto de los datos representados.

---

## 14.16. Informes comparativos

En versiones posteriores podrán crearse informes que comparen diferentes periodos o grupos.

Ejemplo:

```text id="x1a1ye"
Ventas 2025
     ↕
Ventas 2026
```

Podrán calcularse indicadores como:

```text id="n6r1vn"
Valor actual
Valor anterior
Diferencia
Variación porcentual
```

Esta funcionalidad deberá desarrollarse sobre el sistema de métricas existente y no mediante cálculos independientes específicos de cada informe.

---

## 14.17. Informe como documento reproducible

Al igual que ocurre con los gráficos y Dashboards, un informe no debería almacenarse únicamente como un PDF.

Deberá existir una configuración estructurada que permita reconstruirlo.

Conceptualmente:

```text id="f3n6ry"
Informe
├── Dataset
├── filtros
├── componentes
├── cálculos
├── estructura
├── diseño
└── configuración
```

El PDF será el resultado de ejecutar esa configuración.

Esto permitirá posteriormente:

```text id="k9l4xi"
Configuración de informe
        ↓
HTML
        ↓
PDF
```

o incluso:

```text id="6e2w4u"
Configuración
        ↓
Pantalla web
```

sin tener que mantener versiones independientes del mismo informe.

---

## 14.18. Historial y versiones

En futuras versiones podrá existir un historial de generación de informes.

Por ejemplo:

```text id="1i4xkw"
Informe mensual
├── septiembre 2026
├── agosto 2026
├── julio 2026
└── junio 2026
```

Cada generación podrá conservar información como:

* fecha de generación;
* Dataset utilizado;
* versión de configuración;
* filtros;
* periodo;
* usuario;
* archivo generado.

Esta funcionalidad será especialmente útil cuando los informes tengan carácter periódico.

No formará parte necesariamente del MVP inicial.

---

## 14.19. Automatización futura

La existencia de plantillas e informes reproducibles permitirá posteriormente incorporar automatización.

Ejemplo conceptual:

```text id="bl1n4j"
Cada lunes
     ↓
Actualizar fuente
     ↓
Regenerar Dataset
     ↓
Aplicar configuración
     ↓
Generar informe
     ↓
Exportar PDF
```

Posteriormente podría añadirse distribución mediante:

* correo electrónico;
* almacenamiento en una carpeta;
* Google Drive;
* otros servicios.

Estas capacidades no forman parte del MVP inicial.

La arquitectura deberá simplemente evitar impedirlas.

---

## 14.20. Formatos iniciales y futuros

La primera versión podrá priorizar:

| Funcionalidad | Formato |
| ------------- | ------- |
| Dataset       | JSON    |
| Dataset       | CSV     |
| Dataset       | Excel   |
| Tabla         | CSV     |
| Tabla         | Excel   |
| Gráfico       | Imagen  |
| Dashboard     | PDF     |
| Informe       | PDF     |
| Informe       | HTML    |

Otros formatos podrán estudiarse posteriormente.

La implementación concreta podrá variar según las necesidades reales detectadas durante el desarrollo.

---

## 14.21. Límites iniciales

Para mantener controlado el alcance, inicialmente no se incluirán:

* envío automático por correo;
* informes programados;
* firma digital;
* control documental avanzado;
* distribución multiusuario;
* permisos sobre informes;
* generación masiva;
* plantillas extremadamente complejas;
* edición colaborativa;
* integración con múltiples servicios externos;
* generación mediante inteligencia artificial.

Estas capacidades podrán incorporarse posteriormente si el producto demuestra que existe una necesidad real.

---

## 14.22. Relación con Dataset, Vista y Dashboard

La arquitectura conceptual completa queda definida de la siguiente forma:

```text id="g2j6q1"
                    FUENTE
                      ↓
                   DATASET
                      ↓
                    VISTA
                      ↓
             ┌────────┴────────┐
             ↓                 ↓
          GRÁFICO          TABLA / KPI
             ↓                 ↓
             └────────┬────────┘
                      ↓
                  DASHBOARD
                      ↓
                   INFORME
                      ↓
                 EXPORTACIÓN
```

Sin embargo, estas relaciones no deben entenderse necesariamente como una única cadena obligatoria.

Por ejemplo:

```text id="0j8q0f"
Dataset → CSV
```

es perfectamente válido.

También:

```text id="a9l4cj"
Dataset → Gráfico → Imagen
```

o:

```text id="3e3rqa"
Dataset → Dashboard → PDF
```

o:

```text id="xq7z4g"
Dataset → Informe → PDF
```

El sistema deberá permitir diferentes recorridos según el objetivo del usuario.

---

## 14.23. Principios de diseño

La funcionalidad de informes y exportación deberá respetar los siguientes principios:

1. **El Dataset sigue siendo la fuente de verdad.**
2. **Exportar no modifica los datos.**
3. **Exportación e informe son conceptos diferentes.**
4. **Los resultados deberán indicar claramente qué datos representan.**
5. **Las vistas filtradas deberán poder exportarse como tales.**
6. **Los informes deberán conservar el contexto de los datos.**
7. **Los cálculos deberán ser reproducibles.**
8. **El formato de presentación no deberá modificar el Dataset.**
9. **La configuración de un informe deberá poder almacenarse.**
10. **Un PDF generado será un resultado, no necesariamente la fuente editable del informe.**
11. **La trazabilidad del origen deberá mantenerse cuando sea relevante.**
12. **La arquitectura deberá permitir automatización futura sin exigirla en el MVP.**

---

## 14.24. Resultado esperado

Al finalizar esta fase, Excel Dashboard deberá poder cubrir diferentes necesidades de salida:

```text id="y2y6kl"
                         DATASET
                            │
             ┌──────────────┼──────────────┐
             ↓              ↓              ↓
           TABLA          GRÁFICO          KPI
             │              │              │
             └──────────────┼──────────────┘
                            ↓
                        DASHBOARD
                            │
                            ↓
                         INFORME
                            │
             ┌──────────────┼──────────────┐
             ↓              ↓              ↓
            PDF            HTML          DATOS
                                          │
                                  ┌───────┼───────┐
                                  ↓       ↓       ↓
                                 CSV    Excel    JSON
```

El objetivo final no será únicamente mostrar los datos importados, sino permitir que el usuario pueda **transformarlos, analizarlos, visualizarlos, organizarlos y convertirlos en resultados reutilizables o distribuibles**.

La aplicación pasará así de ser un sistema de importación de Excel a una plataforma de procesamiento y presentación de información, manteniendo el Dataset como elemento central y estable de toda la arquitectura.

# 15. Gestión de proyectos y configuración

## 15.1. Objetivo

La aplicación debe permitir organizar el trabajo realizado sobre una fuente de datos mediante el concepto de **Proyecto**.

Un Proyecto representa la configuración necesaria para interpretar una fuente de datos y construir sobre ella vistas, visualizaciones, dashboards e informes.

El Proyecto no debe considerarse una copia del archivo Excel ni un contenedor de sus datos. Su función es conservar la **estructura y configuración del trabajo realizado por el usuario**, permitiendo recuperar y reproducir dicha configuración.

La separación conceptual será:

```text
Fuente de datos
      ↓
Procesamiento
      ↓
Dataset
      ↓
Proyecto
      ├── configuración de tablas
      ├── configuración de columnas
      ├── transformaciones
      ├── vistas
      ├── visualizaciones
      ├── dashboards
      └── informes
```

La configuración constituye la información necesaria para definir cómo debe interpretarse y presentarse la fuente.

---

## 15.2. Concepto de Proyecto

Un Proyecto será la unidad principal de organización dentro de la aplicación.

Podrá representar, por ejemplo:

* Un análisis de ventas.
* Un control de inventario.
* Un seguimiento de indicadores.
* Un análisis de recursos humanos.
* Un cuadro de mando de una organización.
* Cualquier otro trabajo basado en una fuente de datos compatible.

Cada Proyecto tendrá una configuración independiente.

De forma conceptual:

```text
Proyecto
├── identificación
├── fuente
├── hojas
├── tablas
├── columnas
├── transformaciones
├── vistas
├── visualizaciones
├── dashboards
└── informes
```

El nombre del Proyecto será independiente del nombre de la fuente de datos.

Por ejemplo:

```text
Proyecto:
    Control mensual de ventas

Fuente:
    ventas_2026.xlsx
```

---

## 15.3. Información de configuración

La aplicación podrá conservar la información necesaria para reconstruir la configuración del Proyecto.

Entre ella se encuentra:

### Identificación del proyecto

* Identificador interno.
* Nombre del Proyecto.
* Descripción, opcional.
* Fecha de creación.
* Fecha de última modificación.
* Estado del Proyecto.

### Configuración de la fuente

* Tipo de fuente.
* Identificador técnico de la fuente cuando sea necesario.
* Hoja o conjunto de hojas utilizadas.
* Configuración de las tablas.
* Rango de cada tabla.

### Configuración de columnas

* Identificador interno de columna.
* Posición de la columna.
* Nombre utilizado para mostrarla.
* Tipo detectado.
* Tipo seleccionado por el usuario.
* Transformaciones configuradas.
* Reglas de normalización.
* Configuración relacionada con valores especiales.

### Configuración de vistas

* Columnas visibles.
* Orden de las columnas.
* Ordenación.
* Filtros.
* Búsquedas.
* Configuración de paginación cuando proceda.

### Configuración de visualizaciones

* Tipo de gráfico.
* Dataset utilizado.
* Columnas seleccionadas.
* Agrupaciones.
* Agregaciones.
* Filtros.
* Configuración visual.

### Configuración de dashboards

* Nombre.
* Componentes.
* Posición y tamaño de los componentes.
* Filtros globales.
* Configuración individual de cada componente.
* Distribución del dashboard.

### Configuración de informes

* Plantilla.
* Título.
* Periodos utilizados.
* Componentes incluidos.
* Tablas.
* Indicadores.
* Gráficos.
* Configuración de presentación.

---

## 15.4. Separación entre configuración y datos

La aplicación debe mantener una separación clara entre la configuración del Proyecto y los datos contenidos en la fuente.

Por ejemplo, puede formar parte de la configuración:

```text
Columna:
    peso

Tipo:
    Number

Transformaciones:
    trim
    eliminar "kg"

Visualización:
    gráfico de líneas
    eje X → fecha
    eje Y → peso
```

Mientras que los valores reales de las filas pertenecen al Dataset generado a partir de la fuente:

```text
2026-09-01 → 99.95
2026-09-02 → 99.70
2026-09-03 → 99.40
```

Esta separación permite modificar la forma de visualizar o interpretar los datos sin modificar la fuente original.

---

## 15.5. Persistencia de la configuración

La configuración del Proyecto debe poder persistirse para que el usuario no tenga que volver a definir manualmente todo el proceso cada vez que accede a la aplicación.

La persistencia estará orientada a conservar la configuración, no a convertir la aplicación en un almacén permanente de los datos contenidos en los archivos de origen.

La estructura conceptual podría ser:

```text
BBDD
│
├── proyectos
├── fuentes
├── tablas
├── columnas
├── transformaciones
├── vistas
├── visualizaciones
├── dashboards
└── informes
```

Estas entidades representan configuración y relaciones entre elementos del Proyecto.

El Dataset generado durante el procesamiento constituye una representación de trabajo independiente de dicha configuración.

---

## 15.6. Identificación de la fuente

El Proyecto debe mantener información suficiente para saber de qué fuente procede su configuración.

Sin embargo, la identificación de una fuente debe limitarse a la información realmente necesaria.

Por ejemplo, puede ser necesario conservar:

```text
Tipo de fuente: Excel
Identificador de fuente: ...
Hoja: Histórico
Rango: A8:V1187
```

No será necesario conservar de forma permanente una copia del contenido del archivo para representar esta configuración.

La aplicación deberá evitar almacenar información de la fuente que no sea necesaria para reconstruir o ejecutar el Proyecto.

---

## 15.7. Nombres y metadatos

Los nombres utilizados para mostrar información al usuario y los identificadores internos de la aplicación deben mantenerse conceptualmente separados.

Por ejemplo:

```text
Nombre mostrado:
    Peso (kg)

Identificador interno:
    column_05
```

Esto permite modificar posteriormente el nombre mostrado sin romper las referencias internas utilizadas por filtros, gráficos, dashboards o informes.

Los metadatos que puedan revelar información innecesaria sobre el contenido de una fuente deberán minimizarse.

La aplicación debe priorizar la conservación de información estructural y técnica frente a información procedente directamente de los datos.

---

## 15.8. Recuperación de un Proyecto

Cuando un usuario vuelva a acceder a un Proyecto, la aplicación debe poder recuperar su configuración y reconstruir su estructura de trabajo.

El flujo conceptual será:

```text
Abrir Proyecto
      ↓
Cargar configuración
      ↓
Identificar fuente
      ↓
Comprobar configuración
      ↓
Procesar fuente
      ↓
Generar Dataset
      ↓
Recuperar vistas y visualizaciones
      ↓
Mostrar Dashboard / informes
```

La configuración persistida no sustituye a la fuente original.

Si la fuente necesaria para reconstruir el Dataset no está disponible, la aplicación debe informar del problema en lugar de generar datos incorrectos o utilizar silenciosamente otra fuente.

---

## 15.9. Cambios en la configuración

Cuando el usuario modifique una parte relevante de la configuración, la aplicación debe determinar si es necesario regenerar el Dataset o actualizar los elementos dependientes.

Por ejemplo:

```text
Cambiar nombre mostrado
        ↓
No necesariamente requiere regenerar Dataset
```

Mientras que:

```text
Cambiar tipo:
Text → Number
        ↓
Requiere reprocesamiento / validación
        ↓
Regenerar Dataset
```

Del mismo modo:

```text
Modificar transformación
        ↓
Reprocesar columna
        ↓
Validar
        ↓
Regenerar Dataset
```

La aplicación no debe mantener como válida una configuración de Dataset que haya quedado obsoleta debido a cambios en las reglas de interpretación.

---

## 15.10. Estado de configuración

Un Proyecto podrá encontrarse conceptualmente en diferentes estados:

```text
Nuevo
  ↓
Configurando
  ↓
Configurado
  ↓
Validado
  ↓
Dataset generado
  ↓
Disponible para análisis
```

Un cambio posterior puede hacer que determinados elementos vuelvan a requerir validación.

Por ejemplo:

```text
Dataset generado
      ↓
Usuario cambia transformación
      ↓
Configuración modificada
      ↓
Dataset anterior → obsoleto
      ↓
Validación
      ↓
Nuevo Dataset
```

El estado deberá reflejar esta situación para evitar que el usuario trabaje con una representación que ya no corresponde a su configuración actual.

---

## 15.11. Independencia respecto a la presentación

La configuración de un Dashboard, una Vista o un Informe no debe contener una copia de los datos.

Estos elementos deben almacenar únicamente las instrucciones necesarias para presentar o consultar el Dataset.

Por ejemplo:

```text
Dashboard
    └── gráfico de peso
            ├── eje X → fecha
            ├── eje Y → peso
            ├── agregación → AVG
            └── filtro → periodo
```

El gráfico no almacena los valores utilizados para construirlo.

Esto permite que una misma configuración pueda utilizarse sobre un Dataset regenerado a partir de la misma fuente.

---

## 15.12. Eliminación de proyectos

La aplicación deberá permitir eliminar un Proyecto y su configuración asociada.

La eliminación deberá afectar a los elementos pertenecientes al Proyecto, como:

* Configuración de tablas.
* Configuración de columnas.
* Transformaciones.
* Vistas.
* Visualizaciones.
* Dashboards.
* Informes.
* Metadatos asociados.

La eliminación de un Proyecto no deberá modificar la fuente de datos original del usuario.

---

## 15.13. Principios de diseño

La gestión de Proyectos se basará en los siguientes principios:

1. **El Proyecto representa configuración, no una copia del Excel.**
2. **La fuente de datos permanece conceptualmente separada del Proyecto.**
3. **La configuración debe poder recuperarse y reproducirse.**
4. **Los datos y la configuración no deben mezclarse.**
5. **Los identificadores internos deben ser independientes de los nombres mostrados.**
6. **Solo debe conservarse la información necesaria para gestionar el Proyecto.**
7. **Los cambios de configuración deben reflejarse en el estado del Dataset.**
8. **Las vistas, gráficos, dashboards e informes deben almacenar configuración y no copias de los datos.**
9. **La eliminación de un Proyecto no debe modificar la fuente original.**
10. **La arquitectura debe favorecer la minimización de información almacenada y evitar conservar datos que no sean necesarios para el funcionamiento del Proyecto.**

---

## 15.14. Resultado conceptual

Con esta sección, el modelo general de la aplicación queda definido como:

```text
FUENTE
  │
  │ extracción
  ↓
DATASET
  │
  ├──────────────→ VISTAS
  │
  ├──────────────→ VISUALIZACIONES
  │
  ├──────────────→ DASHBOARDS
  │
  └──────────────→ INFORMES


PROYECTO
  │
  ├── configuración de fuente
  ├── definición de tablas
  ├── configuración de columnas
  ├── transformaciones
  ├── vistas
  ├── visualizaciones
  ├── dashboards
  └── informes
```

El Proyecto actúa como la unidad que organiza y conserva la configuración del trabajo, mientras que el Dataset representa los datos normalizados utilizados durante el análisis.

De esta forma, la aplicación puede proporcionar continuidad al trabajo del usuario sin necesitar convertir el Proyecto en una copia permanente de las fuentes de datos.

# 16. Evolución y ampliaciones previstas

## 16.1. Objetivo

El MVP de Excel Dashboard se centra en resolver de forma completa el proceso de transformación de archivos Excel en Datasets normalizados y su posterior análisis y visualización.

Una vez consolidado este flujo, el producto podrá evolucionar incorporando nuevas fuentes de datos, capacidades de interpretación, automatización y funcionalidades de explotación.

Las ampliaciones descritas en esta sección representan líneas de evolución previstas y no forman parte del alcance funcional inicial.

El desarrollo de estas funcionalidades deberá realizarse a partir de las necesidades reales detectadas durante la evolución del producto, evitando introducir complejidad antes de que exista una necesidad concreta.

---

## 16.2. Incorporación de nuevas fuentes de datos

Una de las principales líneas de evolución será permitir que el sistema trabaje con fuentes diferentes de Excel.

Entre las posibles fuentes se encuentran:

* Archivos CSV.
* Google Sheets.
* Archivos almacenados en Google Drive.
* Bases de datos.
* APIs externas.
* Otras fuentes estructuradas.

El objetivo será mantener la separación existente entre la fuente y el Dataset.

Conceptualmente:

```text id="3ojw9u"
Excel ─────────┐
CSV ───────────┤
Google Sheets ─┤
Google Drive ──┤
Base de datos ─┤
API ───────────┘
        ↓
   Extracción
        ↓
Interpretación
        ↓
Normalización
        ↓
     Dataset
```

Cada fuente tendrá su mecanismo específico de extracción, pero todas deberán producir una representación compatible con el Dataset interno siempre que sea posible.

---

## 16.3. Ampliación de los tipos de datos

Los cuatro tipos iniciales:

* Text.
* Number.
* Date.
* Boolean.

podrán ampliarse cuando las necesidades del producto lo justifiquen.

Entre las posibles extensiones se encuentran:

* Porcentajes.
* Monedas.
* Cantidades con unidades.
* Rangos.
* Duraciones.
* Horas.
* Fecha y hora.
* Identificadores.
* Categorías.
* Tipos geográficos.
* Otros tipos semánticos.

La ampliación deberá mantener la separación entre el **tipo almacenado**, el **tipo semántico** y el **formato de presentación** cuando sea necesario.

Por ejemplo, un valor almacenado como:

```text
0.317
```

podrá seguir siendo un número dentro del Dataset y representarse como:

```text
31,7 %
```

sin modificar su valor normalizado.

---

## 16.4. Detección semántica avanzada

La detección inicial se limita a identificar tipos básicos de datos.

En versiones posteriores podría incorporarse una interpretación más avanzada de las columnas.

Por ejemplo:

```text
"Peso (kg)"              → cantidad / peso
"Fecha nacimiento"       → fecha
"Importe (€)"             → moneda
"Porcentaje cumplimiento" → porcentaje
```

Esta interpretación permitiría generar mejores propuestas para filtros, indicadores y visualizaciones.

Sin embargo, la detección semántica deberá considerarse una **propuesta**, no una sustitución de la decisión del usuario.

La configuración explícita realizada por el usuario tendrá prioridad sobre cualquier inferencia automática.

---

## 16.5. Relaciones entre Datasets

Inicialmente cada Dataset será independiente.

Una evolución posterior podría permitir establecer relaciones entre diferentes Datasets.

Por ejemplo:

```text id="m7j7a0"
Dataset Clientes
    cliente_id
         │
         │ relación
         ↓
Dataset Ventas
    cliente_id
```

Esto permitiría construir análisis que combinen diferentes fuentes o tablas.

Las relaciones podrían utilizarse posteriormente para:

* Combinar información.
* Crear indicadores relacionados.
* Aplicar filtros entre Datasets.
* Construir dashboards más complejos.
* Generar informes integrados.

Esta capacidad deberá introducirse únicamente cuando el modelo de Dataset y sus reglas de integridad estén suficientemente consolidados.

---

## 16.6. Automatización de actualización de fuentes

Una evolución natural del producto será permitir que un Proyecto pueda actualizarse automáticamente a partir de su fuente.

Por ejemplo:

```text id="n5e8v4"
Fuente actualizada
       ↓
Importación
       ↓
Procesamiento
       ↓
Validación
       ↓
Nuevo Dataset
       ↓
Dashboard actualizado
```

Esto podría permitir posteriormente:

* Actualizaciones programadas.
* Actualización manual bajo demanda.
* Comprobación de cambios en una fuente.
* Generación automática de informes.
* Procesos periódicos.

La automatización deberá mantener las mismas reglas de validación utilizadas durante el procesamiento manual.

---

## 16.7. Integración con Google Drive y Google Sheets

La integración con servicios externos como Google Drive o Google Sheets permitirá trabajar con documentos almacenados fuera del entorno local del usuario.

La autorización deberá realizarse mediante mecanismos específicos de autenticación y autorización del proveedor, evitando que la aplicación necesite conocer las credenciales del usuario.

La integración deberá separar:

```text id="b4jzq7"
Identidad del usuario
        ↓
Autorización
        ↓
Acceso a la fuente
        ↓
Extracción
        ↓
Dataset
```

Los permisos solicitados deberán limitarse a los necesarios para realizar la operación solicitada.

La configuración de este tipo de fuentes deberá mantenerse separada de los datos obtenidos de ellas.

---

## 16.8. API y reutilización del Dataset

El Dataset podrá convertirse posteriormente en una interfaz de acceso para otras aplicaciones.

Una posible evolución sería disponer de una API:

```text id="6h3j6w"
Fuente
  ↓
Dataset
  ├── Aplicación web
  ├── Dashboard
  ├── Informes
  └── API
```

Esto permitiría que otras aplicaciones consumieran los datos normalizados sin tener que interpretar directamente el Excel original.

La API podría proporcionar posteriormente:

* Consulta de Datasets.
* Filtros.
* Paginación.
* Indicadores.
* Datos para visualizaciones.
* Exportaciones.

Esta capacidad dependerá de que el modelo interno del Dataset haya demostrado ser suficientemente estable.

---

## 16.9. Evolución de la interfaz y arquitectura tecnológica

El MVP comenzará con una arquitectura sencilla basada en PHP, HTML, CSS y JavaScript.

A medida que aumente la complejidad de la aplicación, determinadas partes podrán evolucionar hacia tecnologías o arquitecturas más especializadas.

Entre las posibles evoluciones se encuentran:

* Laravel para estructurar el backend.
* React u otra tecnología frontend para interfaces más complejas.
* APIs independientes.
* Servicios especializados para procesamiento.
* Sistemas de colas para operaciones pesadas.
* Bases de datos para configuración y gestión de proyectos.

Estas decisiones deberán realizarse en función de las necesidades reales del producto y no como requisito previo del MVP.

La evolución tecnológica no deberá alterar los principios fundamentales establecidos en esta especificación.

---

## 16.10. Automatización y generación de informes

Los informes definidos en un Proyecto podrán evolucionar hacia procesos automatizados.

Por ejemplo:

```text id="y7bb3d"
Actualización de fuente
        ↓
Procesamiento
        ↓
Validación
        ↓
Dataset
        ↓
Dashboard
        ↓
Informe
        ↓
Distribución
```

Esto podría permitir posteriormente:

* Generación periódica de informes.
* Informes mensuales o semanales.
* Exportación automática.
* Distribución mediante diferentes canales.
* Historial de informes generados.

Estas funcionalidades deberán mantener la trazabilidad entre la fuente, el Dataset utilizado y el informe generado.

---

## 16.11. Gestión avanzada de usuarios y proyectos

Una evolución posterior podría incorporar funcionalidades orientadas a entornos multiusuario.

Entre ellas:

* Usuarios.
* Organizaciones.
* Proyectos compartidos.
* Permisos.
* Roles.
* Espacios de trabajo.
* Control de acceso.
* Historial de modificaciones.

Estas funcionalidades permitirían transformar la aplicación desde una herramienta individual de análisis hacia una plataforma utilizada por equipos u organizaciones.

---

## 16.12. Evolución hacia una plataforma modular

Una vez que Excel Dashboard y otras fuentes hayan sido desarrolladas, podrá evaluarse qué componentes son realmente comunes entre ellas.

Por ejemplo:

```text id="f0ljwq"
                 ┌── Excel
                 │
                 ├── CSV
Fuentes ─────────┤
                 ├── Google Sheets
                 │
                 └── AD
                       ↓
                 Normalización
                       ↓
                    Dataset
                       ↓
             ┌─────────┼─────────┐
             ↓         ↓         ↓
           Tabla     Gráficos  Dashboard
             │         │         │
             └─────────┼─────────┘
                       ↓
                    Informes
```

En ese momento podrá identificarse qué partes constituyen realmente un núcleo común y extraerlas en componentes reutilizables.

La extracción de dicho núcleo deberá basarse en funcionalidades que hayan demostrado ser comunes en proyectos reales, evitando crear abstracciones prematuras.

---

## 16.13. Criterio para incorporar nuevas funcionalidades

Las futuras ampliaciones deberán evaluarse teniendo en cuenta:

* Necesidad real.
* Reutilización.
* Complejidad introducida.
* Beneficio para el usuario.
* Impacto sobre la arquitectura existente.
* Mantenimiento.
* Seguridad.
* Rendimiento.
* Claridad de uso.

Una funcionalidad no deberá incorporarse únicamente porque sea técnicamente posible.

El crecimiento del producto deberá mantener como principio la existencia de un núcleo sencillo, comprensible y verificable.

---

## 16.14. Visión de evolución

La evolución prevista puede resumirse conceptualmente como:

```text id="j6v3qy"
                 MVP
                  │
                  ↓
             Excel Dashboard
                  │
          ┌───────┴────────┐
          ↓                ↓
    Nuevas fuentes     Más capacidades
          │                │
          ↓                ↓
      CSV / Drive      Tipos avanzados
      Sheets / APIs    Relaciones
          │            Automatización
          └───────┬────────┘
                  ↓
             Dataset común
                  ↓
       ┌──────────┼──────────┐
       ↓          ↓          ↓
   Dashboards  Informes     API
       │          │          │
       └──────────┼──────────┘
                  ↓
          Plataforma modular
```

La evolución del producto deberá realizarse de forma incremental.

El objetivo inicial será construir y validar correctamente el flujo:

```text
Excel
 ↓
Tabla
 ↓
Detección
 ↓
Configuración
 ↓
Transformación
 ↓
Validación
 ↓
Dataset
 ↓
Análisis
 ↓
Dashboard
 ↓
Informe
```

A partir de este núcleo se podrán incorporar nuevas fuentes y capacidades sin perder la separación entre **fuente**, **Dataset**, **configuración** y **presentación**.

La especificación del MVP se considera independiente de estas ampliaciones: una futura funcionalidad solo deberá incorporarse al alcance principal cuando exista una decisión explícita de desarrollarla.
