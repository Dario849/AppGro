<?php
require('system/main.php');
sessionCheck();
$layout = new HTML(title: 'Gestión de Herramientas', uid: $_SESSION['user_id']);
?>

<style>
    :root {
        --primary: #417a7c;
        --primary-foreground: #ffffff;
        --secondary: #2a4f50;
        --accent: #345e5f;
        --destructive: #e74c3c;
        --muted: #1a1a1a;
        --muted-foreground: #888888;
        --card: #2d2d2d;
        --card-foreground: #ffffff;
        --border: #404040;
        --input: #3a3a3a;
        --radius: 8px;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    }

    .herramientas-container {
        border-radius: 10px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        color: white;
    }

    /* Filtros */
    .filtros-section {
        background: var(--card);
        padding: 1.5rem;
        border-radius: var(--radius);
        margin-bottom: 2rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .filtros-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .filtro-group {
        display: flex;
        flex-direction: column;
    }

    .filtro-group label {
        color: var(--muted-foreground);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .filtro-group input,
    .filtro-group select {
        background: var(--input);
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 0.75rem;
        color: white;
        font-size: 0.875rem;
    }

    .filtro-group input:focus,
    .filtro-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(65, 122, 124, 0.2);
    }

    /* Botones */
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--radius);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: var(--primary);
        color: var(--primary-foreground);
    }

    .btn-primary:hover {
        background: #35696b;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--secondary);
        color: white;
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--muted-foreground);
    }

    .btn-outline:hover {
        background: var(--accent);
        color: white;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: var(--card);
        border-radius: var(--radius);
        padding: 2rem;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .modal-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary);
        margin: 0;
    }

    .close-modal {
        background: none;
        border: none;
        color: var(--muted-foreground);
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
    }

    /* Formularios */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        color: var(--muted-foreground);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        background: var(--input);
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 0.75rem;
        color: white;
        font-size: 0.875rem;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
    }

    /* Upload de imágenes */
    .image-upload {
        border: 2px dashed var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .image-upload:hover {
        border-color: var(--primary);
    }

    .image-upload input {
        display: none;
    }

    .image-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 4px;
        overflow: hidden;
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-image {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        background: var(--destructive);
        color: white;
        border: none;
        border-radius: 50%;
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.75rem;
        cursor: pointer;
    }

    /* Lista de herramientas */
    .herramientas-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .herramienta-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
    }

    .herramienta-card:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
    }

    .herramienta-header {
        display: flex;
        justify-content: between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .herramienta-nombre {
        font-size: 1.125rem;
        font-weight: 600;
        color: white;
        margin: 0;
    }

    .herramienta-estado {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        background: var(--secondary);
        color: white;
    }

    .herramienta-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        font-size: 0.875rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        color: var(--muted-foreground);
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: white;
    }

    /* Historial */
    .historial-section {
        margin-top: 2rem;
    }

    .historial-list {
        display: grid;
        gap: 1rem;
    }

    .historial-item {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
    }

    .historial-header {
        display: flex;
        justify-content: between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .historial-fecha {
        color: var(--muted-foreground);
        font-size: 0.875rem;
    }

    .historial-usuario {
        color: var(--primary);
        font-weight: 500;
    }

    .historial-descripcion {
        color: white;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .historial-imagenes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.5rem;
    }

    .historial-imagen {
        aspect-ratio: 1;
        border-radius: 4px;
        overflow: hidden;
    }

    .historial-imagen img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Utilidades */
    .hidden-tools {
        display: none !important;
    }

    .text-center {
        text-align: center;
    }

    .mt-4 {
        margin-top: 2rem;
    }

    .mb-4 {
        margin-bottom: 2rem;
    }

    .space-y-4>*+* {
        margin-top: 1rem;
    }
</style>
<main class="main__content">
    <div class="main_container">
        <div class="main_containerHerramientas">
            <div class="herramientas-container">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1>Gestión de Herramientas</h1>
                    <p class="muted-foreground">Administra y realiza seguimiento de todas las herramientas disponibles
                    </p>
                </div>

                <!-- Filtros -->
                <div class="filtros-section">
                    <div class="filtros-grid">
                        <div class="filtro-group">
                            <label for="filtro-nombre">Nombre</label>
                            <input type="text" id="filtro-nombre" placeholder="Buscar por nombre">
                        </div>
                        <div class="filtro-group">
                            <label for="filtro-marca">Marca</label>
                            <input type="text" id="filtro-marca" placeholder="Filtrar por marca">
                        </div>
                        <div class="filtro-group">
                            <label for="filtro-modelo">Modelo</label>
                            <input type="text" id="filtro-modelo" placeholder="Filtrar por modelo">
                        </div>
                        <div class="filtro-group">
                            <label for="filtro-estado">Estado</label>
                            <select id="filtro-estado">
                                <option value="">Todos los estados</option>
                                <option value="Nuevo">Nuevo</option>
                                <option value="Usado">Usado</option>
                                <option value="Muy usado">Muy usado</option>
                                <option value="Reparado">Reparado</option>
                                <option value="En reparación">En reparación</option>
                                <option value="Roto">Roto</option>
                                <option value="Por reparar">Por reparar</option>
                            </select>
                        </div>
                        <div class="filtro-group">
                            <label for="filtro-tipo">Tipo de herramienta</label>
                            <select id="filtro-tipo">
                                <option value="">Todos los tipos</option>
                            </select>
                        </div>
                        <div class="filtro-group">
                            <label for="filtro-fecha">Fecha compra</label>
                            <input type="date" id="filtro-fecha">
                        </div>
                        <div class="filtro-group">
                            <button class="btn btn-primary" onclick="cargarHerramientas()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Acciones principales -->
                <div class="flex gap-4 mb-4">
                    <button class="btn btn-primary" onclick="mostrarModalNuevaHerramienta()">
                        <i class="fas fa-plus"></i> Nueva Herramienta
                    </button>
                    <button class="btn btn-secondary" onclick="cargarTiposHerramientas()">
                        <i class="fas fa-tools"></i> Gestionar Tipos
                    </button>
                </div>

                <!-- Lista de herramientas -->
                <div id="herramientas-list" class="herramientas-list">
                    <!-- Las herramientas se cargan aquí dinámicamente -->
                </div>

                <!-- Vista de detalle -->
                <div id="detalle-herramienta" class="hidden-tools">
                    <div class="space-y-4">
                        <button class="btn btn-outline" onclick="mostrarLista()">
                            <i class="fas fa-arrow-left"></i> Volver a la lista
                        </button>

                        <div id="detalle-contenido">
                            <!-- El detalle se carga aquí dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Nueva Herramienta -->
            <div id="modal-nueva-herramienta" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Nueva Herramienta</h3>
                        <button class="close-modal" onclick="cerrarModal('modal-nueva-herramienta')">&times;</button>
                    </div>
                    <form id="form-nueva-herramienta" onsubmit="guardarHerramienta(event)">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="herramienta-nombre">Nombre *</label>
                                <input type="text" id="herramienta-nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="herramienta-marca">Marca *</label>
                                <input type="text" id="herramienta-marca" required>
                            </div>
                            <div class="form-group">
                                <label for="herramienta-modelo">Modelo *</label>
                                <input type="text" id="herramienta-modelo" required>
                            </div>
                            <div class="form-group">
                                <label for="herramienta-tipo">Tipo *</label>
                                <select id="herramienta-tipo" required>
                                    <option value="">Seleccionar tipo...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="herramienta-fecha-compra">Fecha de compra *</label>
                                <input type="date" id="herramienta-fecha-compra" required>
                            </div>
                            <div class="form-group">
                                <label for="herramienta-horas_uso">Horas de uso *</label>
                                <input type="number" value="0" id="herramienta-horas_uso" disabled title="Campo Estado es nuevo, modifique si es que posee horas de uso previas a la compra">
                            </div>
                            <div class="form-group">
                                <label for="herramienta-estado">Estado *</label>
                                <select id="herramienta-estado" required onchange=" return toggleInputHorasUso(this);">
                                    <option value="Nuevo">Nuevo</option>
                                    <option value="Usado">Usado</option>
                                    <option value="Muy usado">Muy usado</option>
                                    <option value="Reparado">Reparado</option>
                                    <option value="En reparación">En reparación</option>
                                    <option value="Roto">Roto</option>
                                    <option value="Por reparar">Por reparar</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label for="herramienta-descripcion">Descripción</label>
                                <textarea id="herramienta-descripcion" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-4 mt-4">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-outline"
                                onclick="cerrarModal('modal-nueva-herramienta')">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Agregar Historial -->
            <div id="modal-agregar-historial" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Agregar Registro al Historial</h3>
                        <button class="close-modal" onclick="cerrarModal('modal-agregar-historial')">&times;</button>
                    </div>
                    <form id="form-agregar-historial" onsubmit="guardarHistorial(event)">
                        <input type="hidden-tools-tools" id="historial-herramienta-id">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="historial-descripcion">Descripción *</label>
                                <textarea id="historial-descripcion" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="historial-horas">Horas de uso</label>
                                <input type="number" id="historial-horas" min="0" value="0">
                            </div>
                            <div class="form-group">
                                <label for="historial-fecha">Fecha *</label>
                                <input type="date" id="historial-fecha" required>
                            </div>
                        </div>

                        <div class="form-group full-width mt-4">
                            <label>Imágenes (máximo 5)</label>
                            <div class="image-upload" onclick="document.getElementById('historial-imagenes').click()">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                <p>Haz clic para seleccionar imágenes</p>
                                <input type="file" id="historial-imagenes" multiple accept="image/*"
                                    onchange="previewImages(this)">
                            </div>
                            <div id="image-preview" class="image-preview"></div>
                        </div>

                        <div class="flex gap-4 mt-4">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-outline"
                                onclick="cerrarModal('modal-agregar-historial')">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Variables globales
    let herramientas = [];
    let herramientaSeleccionada = null;
    let imagenesSeleccionadas = [];

    // Cargar datos iniciales
    document.addEventListener('DOMContentLoaded', function () {
        cargarHerramientas();
        cargarTiposHerramientas('#filtro-tipo');
    });
    function toggleInputHorasUso(selectElement) {
        const horasUsoInput = document.getElementById('herramienta-horas_uso');
        if (selectElement.value === 'Nuevo') {
            horasUsoInput.title = 'Campo Estado es nuevo, modifique si es que posee horas de uso previas a la compra';
            horasUsoInput.value = 0;
            horasUsoInput.disabled = true;
        } else {
            horasUsoInput.title='';
            horasUsoInput.disabled = false;
        }
    }
    // Funciones de API
    async function cargarHerramientas() {
        try {
            $.ajax({
                type: "POST",
                url: "/herramientas/listener",
                data: {
                    method: 'getAll',
                    filtros: obtenerFiltros()

                },
                dataType: "json",
                success: function (response) {
                    herramientas = response.data || [];
                    mostrarHerramientas(herramientas);
                }
            });
        } catch (error) {
            console.error('Error al cargar herramientas:', error);
            mostrarError('Error al cargar las herramientas');
        }
    }

    async function cargarTiposHerramientas(element) {
        console.log(element);
        try {
            $.ajax({
                type: "POST",
                url: "/herramientas/listener",
                data: {
                    method: 'getTipos'
                },
                dataType: "json",
                success: function (response) {
                    const select = document.getElementById(element? $(element).attr('id') :'herramienta-tipo');
                    select.innerHTML = '<option value="">Seleccionar tipo...</option>';

                    (response.data || []).forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo.id;
                        option.textContent = tipo.nombre_herramienta;
                        select.appendChild(option);
                    });
                }
            });
        } catch (error) {
            console.error('Error al cargar tipos:', error);
        }
    }

    async function guardarHerramienta(event) {
        event.preventDefault();

        const herramienta = {
            nombre: document.getElementById('herramienta-nombre').value,
            marca: document.getElementById('herramienta-marca').value,
            modelo: document.getElementById('herramienta-modelo').value,
            id_tipo_herramienta: document.getElementById('herramienta-tipo').value,
            fecha_compra: document.getElementById('herramienta-fecha-compra').value,
            estado: document.getElementById('herramienta-estado').value,
            descripcion: document.getElementById('herramienta-descripcion').value,
            horas_uso_total: document.getElementById('herramienta-horas_uso').value
        };

        try {
            $.ajax({
                type: "POST",
                url: "/herramientas/listener",
                data: {
                    method: 'crearHerramienta',
                    herramienta: herramienta
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        cerrarModal('modal-nueva-herramienta');
                        cargarHerramientas();
                        mostrarExito('Herramienta creada correctamente');
                    } else {
                        mostrarError('Error al crear la herramienta');
                    }
                }
            });
        } catch (error) {
            console.error('Error al guardar herramienta:', error);
            mostrarError('Error al crear la herramienta');
        }
    }

    async function guardarHistorial(event) {
        event.preventDefault();

        const formData = new FormData();
        formData.append('method', 'agregarHistorial');
        formData.append('id_herramienta', document.getElementById('historial-herramienta-id').value);
        formData.append('descripcion', document.getElementById('historial-descripcion').value);
        formData.append('horas_uso', document.getElementById('historial-horas').value);
        formData.append('fecha', document.getElementById('historial-fecha').value);

        // Agregar imágenes
        imagenesSeleccionadas.forEach((imagen, index) => {
            formData.append(`imagenes[${index}]`, imagen);
        });

        try {
            $.ajax({
                type: "POST",
                url: "/herramientas/listener",
                data: formData,
                dataType: "json",
                success: function (response) {
                    cerrarModal('modal-agregar-historial');
                    mostrarDetalleHerramienta(herramientaSeleccionada.id);
                    mostrarExito('Registro agregado al historial');
                },
                processData: false,
                contentType: false
            });
        } catch (error) {
            console.error('Error al guardar historial:', error);
            mostrarError('Error al agregar el registro');
        }
    }

    // Funciones de UI
    function obtenerFiltros() {
        return {
            nombre: document.getElementById('filtro-nombre').value,
            marca: document.getElementById('filtro-marca').value,
            modelo: document.getElementById('filtro-modelo').value,
            estado: document.getElementById('filtro-estado').value,
            id_tipo_herramienta: document.getElementById('filtro-tipo').value,
            fecha_compra: document.getElementById('filtro-fecha').value
        };
    }

    function mostrarHerramientas(herramientas) {
        const container = document.getElementById('herramientas-list');

        if (herramientas.length === 0) {
            container.innerHTML = '<div class="text-center">No se encontraron herramientas</div>';
            return;
        }

        container.innerHTML = herramientas.map(herramienta => `
        <div class="herramienta-card" onclick="mostrarDetalleHerramienta(${herramienta.id})">
            <div class="herramienta-header">
                <h3 class="herramienta-nombre">${herramienta.nombre}</h3>
                <span class="herramienta-estado">${herramienta.estado}</span>
            </div>
            <div class="herramienta-info">
                <div class="info-item">
                    <span class="info-label">Marca/Modelo</span>
                    <span class="info-value">${herramienta.marca} ${herramienta.modelo}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha compra</span>
                    <span class="info-value">${herramienta.fecha_compra}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Horas uso</span>
                    <span class="info-value">${herramienta.horas_uso_total || 0}h</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Último uso</span>
                    <span class="info-value">${herramienta.fecha_ultimo_uso || 'Nunca'}</span>
                </div>
            </div>
        </div>
    `).join('');
    }

    function mostrarDetalleHerramienta(id) {
        const herramienta = herramientas.find(h => h.id === id);
        if (!herramienta) return;

        herramientaSeleccionada = herramienta;

        document.getElementById('herramientas-list').classList.add('hidden-tools');
        document.getElementById('detalle-herramienta').classList.remove('hidden-tools');

        // Aquí cargarías el historial completo de la herramienta
        cargarHistorialHerramienta(id);
    }

    function mostrarLista() {
        document.getElementById('detalle-herramienta').classList.add('hidden-tools');
        document.getElementById('herramientas-list').classList.remove('hidden-tools');
        herramientaSeleccionada = null;
    }

    function mostrarModalNuevaHerramienta() {
        cargarTiposHerramientas('#herramienta-tipo');
        document.getElementById('modal-nueva-herramienta').style.display = 'flex';
        document.getElementById('form-nueva-herramienta').reset();
    }

    function mostrarModalAgregarHistorial(herramientaId) {
        document.getElementById('historial-herramienta-id').value = herramientaId;
        document.getElementById('modal-agregar-historial').style.display = 'flex';
        document.getElementById('form-agregar-historial').reset();
        document.getElementById('image-preview').innerHTML = '';
        imagenesSeleccionadas = [];
    }

    function cerrarModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        imagenesSeleccionadas = [];

        if (input.files.length > 5) {
            mostrarError('Máximo 5 imágenes permitidas');
            input.value = '';
            return;
        }

        Array.from(input.files).forEach((file, index) => {
            if (index >= 5) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-image" onclick="removerImagen(${index})">&times;</button>
            `;
                preview.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
            imagenesSeleccionadas.push(file);
        });
    }

    function removerImagen(index) {
        imagenesSeleccionadas.splice(index, 1);
        previewImages({ files: imagenesSeleccionadas });
    }

    // Funciones de utilidad
    function mostrarExito(mensaje) {
        // Implementar notificación de éxito
        console.log('Éxito:', mensaje);
    }

    function mostrarError(mensaje) {
        // Implementar notificación de error
        console.error('Error:', mensaje);
        alert(mensaje);
    }

    // Reemplazar estas funciones placeholder
    async function cargarHistorialHerramienta(id) {
        try {
            $.ajax({
                type: "POST",
                url: "/herramientas/listener",
                data: {
                    method: 'getHistorial',
                    id_herramienta: id
                },
                dataType: "json",
                success: function (response) {
                    if (response.status === 'success') {
                        mostrarDetalleContenido(id, response.data || []);
                    }
                }
            });
        } catch (error) {
            console.error('Error al cargar historial:', error);
            mostrarError('Error al cargar el historial');
        }
    }

    function mostrarDetalleContenido(herramientaId, historial) {
        const herramienta = herramientas.find(h => h.id === herramientaId);
        const container = document.getElementById('detalle-contenido');
        container.innerHTML = `
        <div class="herramienta-card">
            <div class="herramienta-header">
                <h3 class="herramienta-nombre">${herramienta.nombre}</h3>
                <span class="herramienta-estado">${herramienta.estado}</span>
            </div>
            <div class="herramienta-info">
                <div class="info-item">
                    <span class="info-label">Marca/Modelo</span>
                    <span class="info-value">${herramienta.marca} ${herramienta.modelo}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo</span>
                    <span class="info-value">${herramienta.tipo_nombre || 'Sin tipo'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha compra</span>
                    <span class="info-value">${herramienta.fecha_compra}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Horas uso total</span>
                    <span class="info-value">${herramienta.horas_uso_total || 0}h</span>
                </div>
            </div>
            ${herramienta.descripcion ? `
                <div class="mt-4">
                    <span class="info-label">Descripción</span>
                    <p>${herramienta.descripcion}</p>
                </div>
            ` : ''}
        </div>
        
        <div class="flex justify-between items-center mt-6">
            <h3>Historial de la Herramienta</h3>
            <button class="btn btn-primary" onclick="mostrarModalAgregarHistorial(${herramientaId})">
                <i class="fas fa-plus"></i> Agregar Registro
            </button>
        </div>
        
        <div class="historial-section">
            <div class="historial-list">
                ${historial.length > 0 ? historial.map(item => `
                    <div class="historial-item">
                        <div class="historial-header">
                            <div>
                                <div class="historial-usuario">${item.usuario_nombre || 'Usuario'}</div>
                                <div class="historial-fecha">${new Date(item.fecha).toLocaleDateString()}</div>
                            </div>
                            ${item.horas_uso > 0 ? `<div>${item.horas_uso}h de uso</div>` : ''}
                        </div>
                        <div class="historial-descripcion">${item.descripcion}</div>
                        ${item.imagenes_ids ? `
                            <div class="historial-imagenes">
                                <!-- Las imágenes se cargarían aquí -->
                            </div>
                        ` : ''}
                    </div>
                `).join('') : '<div class="text-center">No hay registros en el historial</div>'}
            </div>
        </div>
    `;
    }

</script>