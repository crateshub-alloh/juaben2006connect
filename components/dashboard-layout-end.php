    </main><!-- /.page-content -->
  </div><!-- /.main-content -->
</div><!-- /.dashboard-layout -->

<?php if (!empty($loadCharts)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php endif; ?>
<script src="<?= APP_URL ?>/assets/js/dashboard.js"></script>
<?php if (!empty($extraJs)) foreach ($extraJs as $js) echo "<script src='{$js}'></script>"; ?>
<?php if (!empty($inlineJs)) echo "<script>{$inlineJs}</script>"; ?>
</body>
</html>
