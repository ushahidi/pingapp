		<footer>
		  <div class="outer-footer-wrap">
		    <hr />
		    <div class="inner-footer-wrap">
		      <div class="copyright">
		        <p>&copy;<?php echo date('Y'); ?> <a href="http://ushahidi.com" target="_blank">Ushahidi</a></p>
		      </div>
		      <div class="footer-links">
		        <ul class="footer-links-list">
		          <li><a href="/info/tos">Terms</a></li>
		          <li>|</li>
		          <li><a href="/info/privacy">Privacy</a></li>
		        </ul>
		      </div>
		    </div>
		  </div>
		</footer>

		</div> <!-- end .main-content -->
	</div> <!-- end .main-content-row -->



	<script src="/media/js/vendor/jquery.js"></script>
	<script src="/media/js/foundation/foundation.js"></script>
	<script src="/media/js/foundation/foundation.abide.js"></script>
	<script src="/media/js/foundation/foundation.alerts.js"></script>
	<script src="/media/js/foundation/foundation.clearing.js"></script>
	<script src="/media/js/foundation/foundation.cookie.js"></script>
	<script src="/media/js/foundation/foundation.dropdown.js"></script>
	<script src="/media/js/foundation/foundation.forms.js"></script>
	<script src="/media/js/foundation/foundation.interchange.js"></script>
	<script src="/media/js/foundation/foundation.joyride.js"></script>
	<script src="/media/js/foundation/foundation.magellan.js"></script>
	<script src="/media/js/foundation/foundation.orbit.js"></script>
	<script src="/media/js/foundation/foundation.placeholder.js"></script>
	<script src="/media/js/foundation/foundation.reveal.js"></script>
	<script src="/media/js/foundation/foundation.section.js"></script>
	<script src="/media/js/foundation/foundation.tooltips.js"></script>
	<script src="/media/js/foundation/foundation.topbar.js"></script>
	<script src="/media/js/vendor/jquery.dataTables.min.js"></script>
	<script src="/media/js/vendor/dataTables.foundation.js"></script>
	<script src="/media/js/vendor/intlTelInput.min.js"></script>
	<?php if ($google_analytics):?>
	<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-12063676-30', 'ushahidi.com');
	ga('send', 'pageview');
	</script>
	<?php endif; ?>
	<script>
		$(document).foundation();
		<?php echo $js; ?>
	</script>
</body>
</html>
