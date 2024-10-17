const express = require('express');
const app = express();
const port = 3000;

// Middleware para archivos estáticos
app.use(express.static('public'));

// Rutas para archivos HTML en la carpeta 'views'
app.get('/', (req, res) => {
    res.sendFile(__dirname + '/views/index.html');
});

app.get('/agregar-productos', (req, res) => {
    res.sendFile(__dirname + '/views/agregar-productos.html');
});

app.get('/configuracion', (req, res) => {
    res.sendFile(__dirname + '/views/configuracion.html');
});

app.get('/home', (req, res) => {
    res.sendFile(__dirname + '/views/home.html');
});

app.get('/lista-categorias', (req, res) => {
    res.sendFile(__dirname + '/views/lista-categorias.html');
});

app.get('/lista-clientes', (req, res) => {
    res.sendFile(__dirname + '/views/lista-clientes.html');
});

app.get('/lista-productos', (req, res) => {
    res.sendFile(__dirname + '/views/lista-productos.html');
});

app.get('/lista-usuarios', (req, res) => {
    res.sendFile(__dirname + '/views/lista-usuarios.html');
});

app.get('/lista-ventas', (req, res) => {
    res.sendFile(__dirname + '/views/lista-ventas.html');
});

app.get('/nueva-categoria', (req, res) => {
    res.sendFile(__dirname + '/views/nueva-categoria.html');
});

app.get('/nueva-venta', (req, res) => {
    res.sendFile(__dirname + '/views/nueva-venta.html');
});

app.get('/nuevo-cliente', (req, res) => {
    res.sendFile(__dirname + '/views/nuevo-cliente.html');
});

app.get('/nuevo-producto', (req, res) => {
    res.sendFile(__dirname + '/views/nuevo-producto.html');
});

app.get('/nuevo-usuario', (req, res) => {
    res.sendFile(__dirname + '/views/nuevo-usuario.html');
});

// Inicializa el servidor
app.listen(port, () => {
    console.log(`Servidor escuchando en http://localhost:${port}`);
});