<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Selecione a Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs border-bottom mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active small fw-bold" data-bs-toggle="tab"
                                data-bs-target="#uploadPanel" type="button">Arquivo Local</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small fw-bold" data-bs-toggle="tab"
                                data-bs-target="#urlPanel" type="button">Endereço URL</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="uploadPanel">
                        <div class="drop-zone" id="dropZone">
                            <i class="fa-solid fa-cloud-arrow-up fs-1 mb-2" style="color: var(--itsuki-primary);"></i>
                            <p class="mb-1 fw-medium text-dark">Arraste a imagem aqui</p>
                            <span class="text-muted small">ou clique para explorar o computador</span>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="urlPanel">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Cole o link completo da imagem:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="fa-solid fa-link"></i>
                                </span>
                                <input type="url" id="imgUrlInput" class="form-control"
                                       placeholder="https://exemplo.com/imagem.jpg">
                            </div>
                        </div>
                        <button type="button" id="btnApplyUrl"
                                class="btn btn-primary w-100 rounded-3 small fw-bold text-white">Confirmar Link</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
