<div class="modal fade" id="modal-hapus" tabindex="-1"
     aria-labelledby="modal-hapus-judul" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="#" method="post">
                <?= csrf_field() ?>

                <div class="modal-header">
                    <h2 class="modal-title h5" id="modal-hapus-judul">Konfirmasi penghapusan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">
                        Anda akan menghapus <span data-hapus-jenis>data</span>
                        <strong data-hapus-nama></strong>.
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash" aria-hidden="true"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>