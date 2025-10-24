    <?php $this->section('scripts') ?>
    <script>
        $(document).ready(function() {
            // Find the container and button elements
            const container = $('#packaging-levels-container');
            const addLevelBtn = $('#add-level-btn');
            const calculatedStockDisplay = $('#calculated-stock-display');
            const initialStock = $('#initial_stock');

            // Function to update the total stock calculation
            function updateCalculation() {
                let totalQuantity = 1;
                container.find('.packaging-input').each(function() {
                    const quantity = parseInt($(this).val()) || 1;
                    totalQuantity *= quantity;
                });
                calculatedStockDisplay.val(totalQuantity);
                initialStock.val(totalQuantity); // Update the hidden input
            }

            // Function to create a new packaging level row
            function createNewLevel() {
                const newDiv = `
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="packaging_unit_name[]" placeholder="e.g., 'Carton Boxes'" list="unit-suggestions">
                    <input type="number" class="form-control packaging-input" name="packaging_unit_quantity[]" value="1" min="1" placeholder="Quantity">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-level-btn"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            `;
                container.append(newDiv);
                updateCalculation();
            }

            // Event listener for the 'Add Packaging Level' button
            // This is the core issue. In your `main.php` file, this listener
            // is likely being attached to the button twice.
            // We will make sure it's only attached once.
            addLevelBtn.off('click').on('click', createNewLevel);

            // Use event delegation to handle clicks on dynamically created remove buttons
            container.off('click', '.remove-level-btn').on('click', '.remove-level-btn', function() {
                if (container.find('.input-group').length > 1) {
                    $(this).closest('.input-group').remove();
                    updateCalculation();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot remove last row',
                        text: 'You must have at least one packaging level.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });

            // Use event delegation to handle input changes on dynamically created quantity inputs
            container.off('input', '.packaging-input').on('input', '.packaging-input', updateCalculation);

            // Initial calculation on page load
            updateCalculation();

            // Init Select2 for the supplier dropdown
            $('#supplier_id').select2();
        });
    </script>
    <?php $this->endSection() ?>