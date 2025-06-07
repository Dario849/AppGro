<?php
function renderNavbar(): void
{
?>
  <div class="home">
    <div class="home__navbar" id="sidebar">
      <ul class="home__navbar-list">
        <li class="home__navbar-item"><a href="/dashboard"><button>Dashboard</button></a></li>
        <li class="home__navbar-item"><a href="/"><button>Home</button></a></li>
        <li class="home__navbar-item"><a href="/about"><button>About</button></a></li>
        <li class="home__navbar-item"><a href="/ipsum"><button>Ipsum</button></a></li>
        <li class="home__navbar-item"><a href="/tareas"><button>Tareas</button></a></li>
        <li class="home__navbar-item"><a href="/calendario"><button>Calendario</button></a></li>
        <li class="home__navbar-item"><a href="/tareasdev"><button>Tareas.html</button></a></li>
        <li class="home__navbar-item"><a href="/calendariodev"><button>Calendario.html</button></a></li>
        <!-- … resto de items … -->
      </ul>
    </div>
  </div>
<?php
}
