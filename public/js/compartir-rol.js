(() => {
    const dibujarCaja = (contexto, x, y, ancho, alto, radio) => {
        contexto.beginPath();
        contexto.moveTo(x + radio, y);
        contexto.arcTo(x + ancho, y, x + ancho, y + alto, radio);
        contexto.arcTo(x + ancho, y + alto, x, y + alto, radio);
        contexto.arcTo(x, y + alto, x, y, radio);
        contexto.arcTo(x, y, x + ancho, y, radio);
        contexto.closePath();
    };

    const escribirAjustado = (contexto, texto, x, y, anchoMaximo) => {
        let contenido = String(texto || '');
        while (contenido.length > 3 && contexto.measureText(contenido).width > anchoMaximo) {
            contenido = contenido.slice(0, -2) + '…';
        }
        contexto.fillText(contenido, x, y);
    };

    const crearImagen = (titulo, subtitulo, partidos) => {
        const ancho = 1080;
        const altoCabecera = 230;
        const altoPartido = 160;
        const canvas = document.createElement('canvas');
        canvas.width = ancho;
        canvas.height = altoCabecera + partidos.length * altoPartido + 64;

        const contexto = canvas.getContext('2d');
        if (!contexto) {
            throw new Error('El navegador no pudo crear la imagen.');
        }

        contexto.fillStyle = '#eef2f7';
        contexto.fillRect(0, 0, canvas.width, canvas.height);

        const fondo = contexto.createLinearGradient(0, 0, ancho, altoCabecera);
        fondo.addColorStop(0, '#071332');
        fondo.addColorStop(1, '#087443');
        contexto.fillStyle = fondo;
        contexto.fillRect(0, 0, ancho, altoCabecera);

        contexto.fillStyle = '#a7f3d0';
        contexto.font = '700 22px Arial, sans-serif';
        contexto.fillText('LIGA DE FUTBOL · CALENDARIO OFICIAL', 60, 58);
        contexto.fillStyle = '#ffffff';
        contexto.font = '900 42px Arial, sans-serif';
        contexto.fillText('ROL DE PARTIDOS', 60, 116);
        contexto.font = '700 26px Arial, sans-serif';
        escribirAjustado(contexto, `${titulo}${subtitulo ? ' · ' + subtitulo : ''}`, 60, 169, 950);
        contexto.fillStyle = '#d1fae5';
        contexto.font = '600 17px Arial, sans-serif';
        contexto.fillText(`${partidos.length} partidos · Todos los juegos se disputan en domingo`, 60, 207);

        partidos.forEach((partido, indice) => {
            const y = altoCabecera + 18 + indice * altoPartido;
            dibujarCaja(contexto, 44, y, 992, 142, 18);
            contexto.fillStyle = '#ffffff';
            contexto.fill();

            contexto.fillStyle = '#047857';
            contexto.font = '800 15px Arial, sans-serif';
            contexto.fillText(partido.fase || `PARTIDO ${indice + 1}`, 68, y + 29);

            contexto.fillStyle = '#0f172a';
            contexto.font = '700 23px Arial, sans-serif';
            escribirAjustado(contexto, partido.local, 68, y + 68, 350);
            escribirAjustado(contexto, partido.visitante, 662, y + 68, 350);

            const tieneResultado = partido.goles_local !== null && partido.goles_visitante !== null;
            let marcador = tieneResultado
                ? `${partido.goles_local} - ${partido.goles_visitante}`
                : 'VS';
            if (partido.penales_local !== null && partido.penales_visitante !== null) {
                marcador += ` (P ${partido.penales_local}-${partido.penales_visitante})`;
            }
            contexto.textAlign = 'center';
            contexto.fillStyle = '#0a1a44';
            contexto.font = '900 27px Arial, sans-serif';
            contexto.fillText(marcador, 540, y + 69, 250);

            contexto.fillStyle = '#475569';
            contexto.font = '700 15px Arial, sans-serif';
            contexto.fillText(
                ['DOMINGO', partido.fecha, partido.hora].filter(Boolean).join(' · '),
                540,
                y + 104,
                900
            );
            contexto.font = '600 15px Arial, sans-serif';
            contexto.fillText(partido.lugar || 'Campo por confirmar', 540, y + 128, 900);
            contexto.textAlign = 'left';
        });

        contexto.fillStyle = '#64748b';
        contexto.font = '600 14px Arial, sans-serif';
        contexto.textAlign = 'center';
        contexto.fillText('Comparte este rol con tu equipo · Liga', ancho / 2, canvas.height - 20);
        contexto.textAlign = 'left';

        return canvas;
    };

    const crearArchivo = (canvas, nombre) => {
        const datos = canvas.toDataURL('image/png').split(',')[1];
        const binario = atob(datos);
        const bytes = new Uint8Array(binario.length);
        for (let indice = 0; indice < binario.length; indice++) {
            bytes[indice] = binario.charCodeAt(indice);
        }

        return new File([new Blob([bytes], { type: 'image/png' })], nombre, { type: 'image/png' });
    };

    document.querySelectorAll('[data-compartir-rol]').forEach((boton) => {
        boton.addEventListener('click', async () => {
            const id = boton.dataset.compartirRol;
            const estado = document.getElementById(`estado-compartir-${id}`);

            try {
                const datos = document.getElementById(`datos-rol-${id}`);
                if (!datos) {
                    throw new Error('No se encontraron los partidos para compartir.');
                }

                const partidos = JSON.parse(datos.textContent);
                const canvas = crearImagen(boton.dataset.titulo, boton.dataset.subtitulo, partidos);
                const archivo = crearArchivo(canvas, `${boton.dataset.nombreArchivo || 'rol'}.png`);
                const texto = `${boton.dataset.titulo}${boton.dataset.subtitulo ? ' · ' + boton.dataset.subtitulo : ''}`;

                if (navigator.share && navigator.canShare && navigator.canShare({ files: [archivo] })) {
                    await navigator.share({ files: [archivo], title: texto, text: texto });
                    if (estado) {
                        estado.textContent = 'Elige WhatsApp y el grupo del equipo en el menú para compartir.';
                    }
                    return;
                }

                const url = URL.createObjectURL(archivo);
                const descarga = document.createElement('a');
                descarga.href = url;
                descarga.download = archivo.name;
                descarga.click();
                window.setTimeout(() => URL.revokeObjectURL(url), 1000);
                window.open(`https://wa.me/?text=${encodeURIComponent(texto)}`, '_blank', 'noopener');
                if (estado) {
                    estado.textContent = 'La imagen se descargó. Adjunta el PNG en el chat o grupo de WhatsApp.';
                }
            } catch (error) {
                if (estado) {
                    estado.textContent = error.name === 'AbortError'
                        ? 'Se canceló el menú para compartir.'
                        : `No se pudo preparar la imagen para compartir: ${error.message}`;
                }
            }
        });
    });
})();
