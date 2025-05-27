<?php
function renderNavbar(): void {
?>
  <div class="home">
    <div class="home__navbar" id="sidebar">
      <ul class="home__navbar-list">
        <li class="home__navbar-item"><a href="/dashboard">Menu</a></li>
        <li class="home__navbar-item"><a href="/">Home</a></li>
        <li class="home__navbar-item"><a href="/about">About</a></li>
        <li class="home__navbar-item"><a href="/ipsum">Ipsum</a></li>
        <!-- … resto de items … -->
      </ul>
    </div>
  </div>
<?php
}
