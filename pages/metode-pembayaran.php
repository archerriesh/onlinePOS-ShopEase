<?php
$pageCSS = '../css/metode-pembayaran.css';
include '../includes/header-main.php';

$payment = $_SESSION['payment'] ?? 'Virtual Account';
$sub     = $_SESSION['sub_payment'] ?? 'BCA';
?>

<div class="payment-page">
    <div class="payment-card">

        <h2>Select a payment method</h2>

        <!-- ===== VIRTUAL ACCOUNT ===== -->
        <div class="method">
            <label class="radio">
                <input type="radio" name="payment" value="Virtual Account" <?= $payment === 'Virtual Account' ? 'checked' : '' ?>>
                <span class="custom-radio"></span>
                Virtual Account
            </label>

            <div class="bank-options">
                <span class="bank" data-value="BCA">BCA</span>
                <span class="bank" data-value="Mandiri">Mandiri</span>
                <span class="bank" data-value="BSI">BSI</span>
            </div>
        </div>

        <!-- ===== E-WALLET ===== -->
        <div class="method">
            <label class="radio">
                <input type="radio" name="payment" value="E-Wallet" <?= $payment === 'E-Wallet' ? 'checked' : '' ?>>
                <span class="custom-radio"></span>
                E-Wallet
            </label>

            <div class="wallet-options">
                <span class="wallet" data-value="DANA">DANA</span>
                <span class="wallet" data-value="ShopeePay">ShopeePay</span>
                <span class="wallet" data-value="GoPay">GoPay</span>
            </div>
        </div>

        <!-- ===== QRIS ===== -->
        <div class="method">
            <label class="radio">
                <input type="radio" name="payment" value="QRIS" <?= $payment === 'QRIS' ? 'checked' : '' ?>>
                <span class="custom-radio"></span>
                QRIS
            </label>
        </div>

        <button class="choose-btn">Choose this payment</button>

    </div>
</div>

<script>
const radios    = document.querySelectorAll('input[name="payment"]');
const banks     = document.querySelectorAll('.bank');
const wallets   = document.querySelectorAll('.wallet');
const bankBox   = document.querySelector('.bank-options');
const walletBox = document.querySelector('.wallet-options');
const chooseBtn = document.querySelector('.choose-btn');

let selectedSub = "<?= $sub ?>";

/* ===== RESET ALL ===== */
function resetAll() {
    banks.forEach(b => b.classList.remove('active', 'disabled'));
    wallets.forEach(w => w.classList.remove('active', 'disabled'));
    bankBox.classList.remove('active');
    walletBox.classList.remove('active');
}

/* ===== UPDATE BUTTON ===== */
function updateButtonState() {
    const selectedRadio = document.querySelector('input[name="payment"]:checked');

    if (!selectedRadio) {
        chooseBtn.disabled = true;
        return;
    }

    if (selectedRadio.value === 'QRIS') {
        chooseBtn.disabled = false;
        return;
    }

    chooseBtn.disabled = !selectedSub;
}

/* ===== INITIAL LOAD (INI YANG PENTING) ===== */
(function init() {
    resetAll();

    const checkedRadio = document.querySelector('input[name="payment"]:checked');

    if (!checkedRadio) {
        chooseBtn.disabled = true;
        banks.forEach(b => b.classList.add('disabled'));
        wallets.forEach(w => w.classList.add('disabled'));
        return;
    }

    if (checkedRadio.value === 'Virtual Account') {
        bankBox.classList.add('active');
        wallets.forEach(w => w.classList.add('disabled'));

        banks.forEach(b => {
            if (b.dataset.value === selectedSub) {
                b.classList.add('active');
            }
        });
    }

    if (checkedRadio.value === 'E-Wallet') {
        walletBox.classList.add('active');
        banks.forEach(b => b.classList.add('disabled'));

        wallets.forEach(w => {
            if (w.dataset.value === selectedSub) {
                w.classList.add('active');
            }
        });
    }

    if (checkedRadio.value === 'QRIS') {
        banks.forEach(b => b.classList.add('disabled'));
        wallets.forEach(w => w.classList.add('disabled'));
    }

    updateButtonState();
})();

/* ===== RADIO CHANGE ===== */
radios.forEach(radio => {
    radio.addEventListener('change', () => {
        selectedSub = null;
        resetAll();

        if (radio.value === 'Virtual Account') {
            bankBox.classList.add('active');
            wallets.forEach(w => w.classList.add('disabled'));
        }
        else if (radio.value === 'E-Wallet') {
            walletBox.classList.add('active');
            banks.forEach(b => b.classList.add('disabled'));
        }
        else {
            banks.forEach(b => b.classList.add('disabled'));
            wallets.forEach(w => w.classList.add('disabled'));
        }

        updateButtonState();
    });
});

/* ===== SUB OPTION CLICK ===== */
banks.forEach(bank => {
    bank.addEventListener('click', () => {
        if (bank.classList.contains('disabled')) return;

        banks.forEach(b => b.classList.remove('active'));
        bank.classList.add('active');
        selectedSub = bank.dataset.value;

        updateButtonState();
    });
});

wallets.forEach(wallet => {
    wallet.addEventListener('click', () => {
        if (wallet.classList.contains('disabled')) return;

        wallets.forEach(w => w.classList.remove('active'));
        wallet.classList.add('active');
        selectedSub = wallet.dataset.value;

        updateButtonState();
    });
});

/* ===== SUBMIT ===== */
chooseBtn.addEventListener('click', () => {
    if (chooseBtn.disabled) return;

    const selectedRadio = document.querySelector('input[name="payment"]:checked');

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'set-payment.php';

    form.innerHTML = `
        <input type="hidden" name="payment" value="${selectedRadio.value}">
        <input type="hidden" name="sub_payment" value="${selectedSub ?? ''}">
    `;

    document.body.appendChild(form);
    form.submit();
});
</script>

<?php include '../includes/footer.php'; ?>
