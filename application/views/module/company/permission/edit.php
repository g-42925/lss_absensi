<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="modal-body">
  <div class="text-center mb-4">
    <h3 class="role-title mb-2">Edit Data</h3>
    <p class="text-muted">Set permissions</p>
  </div>
  <!-- Add role form -->
  <form class="row g-3" action="<?=base_url('company/permission/edit_proses/'.$edit['permission_id']);?>" method="POST">
    <div class="col-12 mb-1">
      <label class="form-label">Label<i class="text-danger">*</i></label>
      <input type="text" name="nama" class="form-control" placeholder="..." value="<?=$edit['nama_permission'];?>" required />
    </div>
    <div class="col-12">
      <div class="table-responsive">
        <table class="table table-flush-spacing">
          <tbody>
            <tr>
              <td class="text-nowrap text-center">
                <div class="d-flex">
                  <div class="form-check me-3 me-lg-5">
                    <input class="form-check-input" type="checkbox" name="tambah" <?php if ($edit['tambah']=='y') echo 'checked'; ?> id="userManagementRead" />
                    <label class="form-check-label" for="userManagementRead"> Tambah </label>
                  </div>
                  <div class="form-check me-3 me-lg-5">
                    <input class="form-check-input" type="checkbox" name="edit" <?php if ($edit['edit']=='y') echo 'checked'; ?> id="userManagementWrite" />
                    <label class="form-check-label" for="userManagementWrite"> Edit </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="hapus" <?php if ($edit['hapus']=='y') echo 'checked'; ?> id="userManagementCreate" />
                    <label class="form-check-label" for="userManagementCreate"> Hapus </label>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-12 text-center mt-4">
      <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan Data</button>
      <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">
        Batal
      </button>
    </div>
  </form>
</div>

