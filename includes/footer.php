<?php if (is_logged_in()): ?>
    </main>
  </div>
</div>
<?php else: ?>
</main>
<footer class="public-footer">
  <p>ResQFood &middot; Turning surplus into sustenance.</p>
</footer>
<?php endif; ?>
<script src="<?= BASE_URL ?>/assets/app.js"></script>
</body>
</html>
