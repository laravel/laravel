<style>
    .qr-flotante {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background-color: white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .qr-flotante:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* Opcional: estilo para el tooltip o popover */
    .qr-popover {
        max-width: 300px;
    }
</style>