# 🎨 FLOCK — Documento de Diseño y Guía de Estilo Visual
**Proyecto Intermodular:** Diseño de Interfaces Web (DIW - RA1)  
**Entidad Municipal:** Ayuntamiento de Villaverde de la Ribera (Concejalía de Cultura)  
**Aplicación:** FLOCK — Agenda Cultural Municipal  
**Versión:** 1.0 (Sprint 1)  

---

## 🎯 1. Comunicación Visual, Público Objetivo y Filosofía de Diseño (RA1.1)

### 1.1. Filosofía de Comunicación Visual
El diseño de **FLOCK** busca derribar la percepción tradicional de la web pública como un entorno burocrático, plano y frío. La interfaz se fundamenta en tres principios:
1. **Claridad Institucional y Confianza:** Jerarquía visual limpia, encabezados reconocibles y tipografía de máxima legibilidad para garantizar la accesibilidad universal de la ciudadanía.
2. **Dinamismo Contemporáneo:** Inspiración en portales culturales modernos (Festivales, Exposiciones, Salas de Conciertos), empleando acentos de color vibrantes para las distintas disciplinas culturales y un **calendario híbrido** que invita a la exploración visual.
3. **Inmediatez y Cero Fricción:** Los flujos clave (consultar aforo de un evento y reservar plaza) se resuelven en un solo clic visual, con estados interactivos claros y sin elementos distractores.

### 1.2. Segmentación del Público y Decisiones Visuales
* **Ciudadanía General y Familias:** Requieren contraste elevado, fuentes legibles de buen tamaño, botones grandes con área de pulsación cómoda (mínimo 44x44px para dedos en pantallas táctiles) e información de acceso directa (fecha, hora, ubicación y si es gratuito).
* **Jóvenes y Colectivos Artísticos (16-35 años):** Responden a un diseño fresco con tarjetas con efecto *hover* suave, previsualización gráfica de los artistas y un conmutador de tema accesible / oscuro.
* **Técnicos Municipales:** Necesitan una interfaz administrativa sobria, densa en datos, con tablas legibles, estados de aforo mediante barras semafóricas y acciones rápidas de edición/baja.

---

## 🎨 2. Paleta de Colores y Tipografía (RA1.2)

### 2.1. Paleta de Color Corporativa y Semántica
La selección cromática equilibra el tono institucional municipal con la energía cultural urbana, garantizando ratios de contraste superiores a **4.5:1 (Nivel AA de las Pautas WCAG 2.1)** para texto normal y **3:1** para texto grande.

| Variable CSS | Valor Hex | Muestra | Rol y Justificación Visual |
| :--- | :---: | :---: | :--- |
| `--color-primary` | `#1d3557` | 🟦 Azul Noche | Color institucional del Ayuntamiento. Evoca seriedad, solidez y fiabilidad pública. Empleado en barras de navegación, cabeceras y títulos principales. |
| `--color-secondary` | `#457b9d` | 🩵 Azul Acero | Color de soporte para subtítulos, bordes secundarios, iconos informativos y elementos de navegación activa. |
| `--color-accent` | `#e63946` | 🔴 Coral Festivo | Color de acento de alta visibilidad para llamadas a la acción (*CTAs* principales, botón "Inscribirme", badges de festivales y fechas destacadas en el calendario). |
| `--color-bg-light` | `#f8f9fa` | ⚪ Blanco Humo | Fondo general claro para descanso visual y máxima comodidad de lectura prolongada. |
| `--color-surface` | `#ffffff` | ⬜ Blanco Puro | Fondo de tarjetas, contenedores de contenido y formularios para generar elevación y contraste sobre el fondo general. |
| `--color-text-main` | `#212529` | ⬛ Gris Carbón | Texto principal de alto contraste frente a blanco (#ffffff ratio 14.5:1), evitando el negro puro `#000` para reducir fatiga visual. |
| `--color-text-muted` | `#6c757d` | 🔘 Gris Medio | Metadatos secundarios (fecha de publicación, dirección secundaria, aforo complementario). |

#### Paleta de Identificación por Categoría Cultural (Badges):
* **Festivales y Encuentros:** `#e63946` (Rojo Coral)
* **Cinefórum y Audiovisual:** `#457b9d` (Azul Cine)
* **Música en Directo:** `#2a9d8f` (Verde Esmeralda)
* **Arte Urbano y Exposiciones:** `#e76f51` (Naranja Terracota)
* **Charlas y Conferencias:** `#6a4c93` (Púrpura Intelectual)

### 2.2. Tipografía Corporativa
Se establece un emparejamiento tipográfico moderno y neutro disponible de forma nativa en sistemas operativos y con respaldo en Google Fonts:

* **Familia Tipográfica Principal (Cuerpo y Encabezados):** `Inter, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif`
  * **Justificación:** *Inter* es una de las familias tipográficas más legibles del ecosistema web actual, diseñada específicamente para pantallas de ordenador y móviles con excelente diferenciación de glifos conflictivos (ej. `I` mayúscula, `l` minúscula y `1` número).
  * **Pesos empleados:**
    * `400 (Regular)`: Cuerpo de texto, descripciones y datos auxiliares.
    * `500 (Medium)`: Elementos de navegación, etiquetas de formulario y botones.
    * `700 (Bold)`: Títulos de sección (`h1`, `h2`, `h3`) y nombres de eventos.

---

## 📐 3. Guía de Estilo y Componentes Reutilizables (RA1.3)

### 3.1. Botones (`.btn`)
* **Botón Primario (`.btn-primary-flock`):** Fondo `#e63946`, texto blanco, bordes redondeados (`border-radius: 8px`), sombra sutil (`box-shadow: 0 2px 4px rgba(230, 57, 70, 0.2)`).  
  * *Hover:* Oscurecimiento suave a `#d62828` y elevación de 1px.  
  * *Focus visible:* Contorno accesible de 2px en color `#457b9d`.
* **Botón Secundario (`.btn-outline-flock`):** Fondo transparente, borde de 2px en `#1d3557`, texto `#1d3557`. Ideal para filtros secundarios ("Ver ficha técnica", "Volver al catálogo").
* **Botón Deshabilitado / Agotado (`.btn-disabled`):** Fondo `#e9ecef`, texto `#6c757d`, cursor `not-allowed`.

### 3.2. Tarjetas de Eventos (`.event-card`)
* Estructura vertical con imagen superior en proporción 16:9 (`object-fit: cover`).
* Contenedor con borde fino de 1px `#e2e8f0` y radio de 12px.
* Cabecera interna con *badge* de categoría en esquina superior y distintivo de precio (Gratuito / 3.50€).
* Cuerpo con título a dos líneas como máximo (`line-clamp: 2`), metadatos con iconos vectoriales SVG limpios (calendario, reloj y ubicación municipal) y barra indicadora de aforo restante.
* Pie de tarjeta con botón de llamada a la acción ("Detalles e Inscripción").

### 3.3. Componente de Calendario Híbrido (`.flock-calendar`)
* Cabecera del calendario con navegación mes anterior / siguiente (`< Noviembre 2026 >`).
* Rejilla de 7 columnas (Lunes a Domingo) con cabecera de días abreviada.
* Días del mes con número claro; los días que cuentan con eventos programados muestran un indicador visual circular luminoso (*dot badge*) debajo del número.
* Al hacer clic en un día activo, se despliega o filtra dinámicamente la lista de tarjetas correspondientes a esa fecha.

### 3.4. Alertas y Mensajes de Feedback (`.alert-flock`)
* **Éxito (Confirmación de Reserva):** Fondo `#d1e7dd`, borde `#a3cfbb`, texto `#0f5132`.
* **Aviso (Aforo Últimas Plazas):** Fondo `#fff3cd`, borde `#ffe69c`, texto `#664d03`.
* **Error / Agotado:** Fondo `#f8d7da`, borde `#f1aeb5`, texto `#842029`.

---

## 🛠️ 4. Tecnologías de Diseño y Sistema de Plantillas (RA1.4)

### 4.1. Estrategia de Tecnologías: Bootstrap + CSS Nativo Puro
Siguiendo las decisiones del equipo para maximizar el control técnico y cumplir al 100% con la rúbrica de DIW:
1. **Bootstrap 5 (Integración Quirúrgica):**
   * Se utiliza mediante CDN para el **menú de navegación colapsable accesible (`navbar`)** y el **sistema de rejilla fluida (`container`, `row`, `col-12 col-md-6 col-lg-4`)** para adaptar automáticamente el número de columnas a cada resolución.
2. **Hoja de Estilos Propia (`public/css/styles.css`):**
   * Se definen variables CSS (`:root`) para temas y colores.
   * Se declaran estilos nativos para el calendario híbrido, las tarjetas de eventos, la tipografía corporativa y las microinteracciones.
3. **Hoja de Estilos Alternativa (`public/css/theme-dark.css`):**
   * Sobrescribe las variables CSS para invertir la superficie a fondos oscuros `#121212` / `#1e1e1e` y textos claros `#f1f1f1`, ofreciendo un tema accesible de alto contraste conmutado desde la cabecera.

### 4.2. Sistema de Plantillas Modulares PHP
Para evitar duplicación de código y garantizar coherencia en todas las vistas, la interfaz se ensambla a partir de componentes PHP reutilizables en `views/layout/`:
* `header.php`: Declaración de metadatos HTML5, viewport responsive, carga de Bootstrap y `styles.css`.
* `navbar.php`: Barra de navegación institucional, enlaces a catálogo, calendario, conmutador de tema y acceso a administración.
* `footer.php`: Créditos institucionales del Ayuntamiento, enlaces de contacto, accesibilidad y cierre de scripts.

---

## 📐 5. Wireframes Conceptuales de las Vistas Principales

### 5.1. Wireframe: Vista Principal (Home & Calendario Híbrido) — Escritorio y Móvil

```text
+-------------------------------------------------------------------------------+
| [Escudo Ayuntamiento]  FLOCK - Agenda Cultural    [Inicio] [Categorías] [Admin]| [🌓 Tema]
+-------------------------------------------------------------------------------+
|  HERO BANNER: "Descubre la vida cultural de Villaverde de la Ribera"          |
|  [ Buscador de actividades...                ] [ Filtro Categoría v ] [Buscar]|
+-------------------------------------------------------------------------------+
|                                                                               |
|  CALENDARIO MENSUAL: < Noviembre 2026 >                                       |
|  +-----+-----+-----+-----+-----+-----+-----+                                  |
|  | Lun | Mar | Mie | Jue | Vie | Sab | Dom |                                  |
|  +-----+-----+-----+-----+-----+-----+-----+                                  |
|  |     |     |     |     |  1  |  2  |  3  |                                  |
|  |  4  |  5  |  6  |  7  |  8  |  9  | 10  |                                  |
|  | 11  | 12  | 13  | 14  | 15  | 16  | 17  |                                  |
|  | 18  | 19  | 20* | 21* | 22* | 23  | 24* |  (* = Día con evento programado) |
|  | 25* | 26  | 27* | 28* | 29* | 30  |     |                                  |
|  +-----+-----+-----+-----+-----+-----+-----+                                  |
+-------------------------------------------------------------------------------+
|  PRÓXIMAS ACTIVIDADES CULTURALES (Grid Responsive: 3 cols PC, 1 col Móvil)   |
|                                                                               |
|  +--------------------+  +--------------------+  +--------------------+       |
|  | [IMAGEN 16:9]      |  | [IMAGEN 16:9]      |  | [IMAGEN 16:9]      |       |
|  | (Festivales)  Gratis|  | (Cinefórum)  Gratis|  | (Música)     3.50€|       |
|  | Festival Urbano... |  | Ciclo Cine Social  |  | Noche Acústica...  |       |
|  | 📅 20 Nov - 18:00h |  | 📅 21 Nov - 19:30h |  | 📅 22 Nov - 20:00h |       |
|  | 📍 Plaza Artes     |  | 📍 Cine Central    |  | 📍 Espacio La Nave |       |
|  | [Plazas: 320/400]  |  | [Plazas: 65/80]    |  | [Plazas: 85/100]   |       |
|  | [Ver Detalle/Res.] |  | [Ver Detalle/Res.] |  | [Ver Detalle/Res.] |       |
|  +--------------------+  +--------------------+  +--------------------+       |
+-------------------------------------------------------------------------------+
| FOOTER: © 2026 Ayuntamiento de Villaverde - Concejalía de Cultura             |
+-------------------------------------------------------------------------------+
```

---

### 5.2. Wireframe: Ficha de Detalle del Evento y Formulario de Inscripción

```text
+-------------------------------------------------------------------------------+
| [ < Volver a la Agenda ]                                                      |
+-------------------------------------------------------------------------------+
|                                                                               |
|  +-------------------------------------+  +--------------------------------+  |
|  | [ IMAGEN PRINCIPAL EN ALTA RES ]    |  | RESERVA TU PLAZA               |  |
|  |                                     |  |                                |  |
|  | TITULO: Noche de Música Acústica    |  | Aforo Disponible: 85 / 100     |  |
|  | Categoría: Música en Directo        |  | Precio: 3.50 €                 |  |
|  | 📅 22 de Noviembre 2026 - 20:00h    |  |                                |  |
|  | 📍 Espacio Joven "La Nave"          |  | [ Nombre y Apellidos *       ] |  |
|  | Dirección: Calle Ribera 14          |  | [ Correo Electrónico *       ] |  |
|  |                                     |  | [ Teléfono de Contacto *     ] |  |
|  | DESCRIPCIÓN:                        |  | Plazas a reservar: [ 1 a 5 v ] |  |
|  | Concierto íntimo con tres bandas    |  | [ Observaciones (opcional)   ] |  |
|  | locales emergentes en formato acúst.|  |                                |  |
|  |                                     |  | [ Confirmar Inscripción ]      |  |
|  | AUDIO PROMOCIONAL:                  |  | (Envío POST seguro con código) |  |
|  | [ ▶ Reproducir presentación cultural]  |                                |  |
|  +-------------------------------------+  +--------------------------------+  |
|                                                                               |
+-------------------------------------------------------------------------------+
```

---

### 5.3. Wireframe: Panel de Gestión Municipal (Concejalía de Cultura)

```text
+-------------------------------------------------------------------------------+
| [Escudo] FLOCK Admin - Panel de Gestión Cultural       [Usuario: Laura] [Salir]|
+-------------------------------------------------------------------------------+
| [ + Publicar Nuevo Evento ]   [ Filtrar por Estado v ]   [ Buscar actividad ] |
+-------------------------------------------------------------------------------+
| LISTADO DE EVENTOS PROGRAMADOS:                                               |
| +---------------------------------------------------------------------------+ |
| | Título          | Fecha / Hora | Espacio        | Inscritos | Acciones    | |
| +-----------------+--------------+----------------+-----------+-------------+ |
| | Festival Otoño  | 20/11 - 18:00| Plaza Artes    | 80 / 400  | [✏️] [👥] [🗑️]| |
| | Ciclo Cine      | 21/11 - 19:30| Cine Central   | 15 / 80   | [✏️] [👥] [🗑️]| |
| | Música Acústica | 22/11 - 20:00| Espacio La Nave| 15 / 100  | [✏️] [👥] [🗑️]| |
| +---------------------------------------------------------------------------+ |
|                                                                               |
|  Acciones: [✏️] Editar Evento | [👥] Ver Lista de Inscritos | [🗑️] Cancelar     |
+-------------------------------------------------------------------------------+
```

---

## 📌 6. Conclusiones de Diseño

El presente documento de diseño garantiza una interfaz atractiva, homogénea y rigurosamente alineada con los estándares de **DIW (RA1)**:
* Se adapta a cualquier pantalla combinando la agilidad del grid y navbar de Bootstrap con el detalle cuidado de estilos propios en CSS.
* Proporciona un marco estructurado y modular que los programadores pueden implementar directamente en PHP y maquetar en el **Sprint 1**.
