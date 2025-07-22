<style>
    .split-screen-container {
  position: relative;
  overflow: visible;
}

.close-split-screen-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: #e74c3c;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  font-size: 1.2rem;
  cursor: pointer;
  z-index: 1000;
}
</style>


<div class="split-screen-container" style="display: flex; gap: 10px; height: 750px; position: relative; overflow: visible;">
    <button type="button" class="close-split-screen-btn"
      style="position: absolute; top: 1px; right: 1px; background: #e74c3c; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 1.2rem; cursor: pointer; z-index: 1000;"
      title="Close">&times;</button>
  <!-- LEFT: Database Items -->
  <div style="flex: 1; border: 1px solid #ccc; overflow-y: auto; background: #fff;">
    <h3>Database Items</h3>
    <table style="width: 100%;">
      <thead>
        <tr>
          <th>Item</th>
          <th>Description</th>
          <th>Quantity</th>
        </tr>
      </thead>
      <!--changes made by niveditha-->
      <tbody>
        @foreach($po->items as $item)
        <tr>
          <td>{{ $item->item_number_doc}}</td>
          <td>{{ $item->short_text}}</td>
          <td>{{ $item->purchase_order_qty }}</td>
        </tr>
        @endforeach
      </tbody>
      <!--changes end-->
    </table>
  </div>
  <!-- RIGHT: PDF Preview + Upload -->
  <div style="flex: 1; border: 1px solid #ccc; background: #fff;">
    <h3>PDF Preview</h3>
    @if($po->po_pdf)
      <iframe src="{{ Storage::url($po->po_pdf) }}" style="width: 100%; height: 650px; border: none;"></iframe>
    @else
      <div style="color: #888; text-align: center; margin-top: 100px;">No PDF attached yet.</div>
    @endif

    <!-- PDF Upload Form -->
    <form class="attach-pdf-form" data-poid="{{ $po->id }}" enctype="multipart/form-data" style="margin-top: 20px;">
      <input type="file" name="po_file" accept="application/pdf" required>
      <button type="submit" style="margin-left: 10px; color : white; background-color: green">Upload PDF</button>
      <span class="upload-status" style="margin-left: 10px; color: #2e7d32;"></span>
    </form>
  </div>
</div>
