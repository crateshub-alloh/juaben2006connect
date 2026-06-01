<!-- Chart.js (loaded only when dashboard pages include it) -->
<?php if (!empty($loadCharts)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php endif; ?>

<!-- Main JS -->
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<?php if (!empty($extraJs)) foreach ($extraJs as $js) echo "<script src='{$js}'></script>"; ?>
</body>
</html>
