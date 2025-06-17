<?php
function renderNavbar(): void
{
?>
  <div class="home">
    <div class="home__navbar" id="sidebar">
      <!-- DIRECCIONES, ELEMENTOS SECCION SUPERIOR DE LA BARRA -->
      <ul class="home__navbar-TopList">
        <!-- <li class="home__navbar-item"><a href="/"><button>Home</button></a></li> -->
        <!-- <li class="home__navbar-item"><a href="/about"><button>About</button></a></li>
        <li class="home__navbar-item"><a href="/ipsum"><button>Ipsum</button></a></li> -->
        <li class="home__navbar-item"><a href="/dashboard"><button>Dashboard</button></a></li>
        <li class="home__navbar-item"><a href="/tareas"><button>Tareas</button></a></li>
        <li class="home__navbar-item"><a href="/calendario"><button>Calendario</button></a></li>
        <li class="home__navbar-item"><a href="/tareasdev"><button>Tareas.html</button></a></li>
        <li class="home__navbar-item"><a href="/calendariodev"><button>Calendario.html</button></a></li>
        <!-- … resto de items … -->
      </ul>
      <!-- UTILIDADES, ELEMENTOS SECCION INFERIOR DE LA BARRA -->
      <ul class="home__navbar-BottomList">
        <li class="home__navbar-item"><a href="/logout"><button>Log-out</button></a></li>

      </ul>
    </div>
  </div>
<?php
}
