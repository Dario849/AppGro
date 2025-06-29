<?php
function alertBox($error, $success)
{ ?>
  <script>
    $(document).ready(function () {
      // Select the button by its ID and attach a click event handler
      $('#btnClose').on('click', function () {
        // Code to execute when the button is clicked
        $("#alertDiv").remove();
      });
    }); 
  </script>
  <?php
  if ($success != 0) {
    ?>
    <!-- From Uiverse.io by seyed-mohsen-mousavi -->
    <div id="alertDiv" class="flex flex-col gap-2 w-60 sm:w-72 text-[10px] sm:text-xs z-50">
      <div
        class="succsess-alert cursor-default flex items-center justify-between w-full h-12 sm:h-14 rounded-lg bg-[#232531] px-[10px]">
        <div class="flex gap-2">
          <div class="text-[#2b9875] bg-white/5 backdrop-blur-xl p-1 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
              class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"></path>
            </svg>
          </div>
          <div>
            <p id="headSuccessText" class="text-white"><?php
            echo explode('-', $success)[0]; ?> </p>
            <p id="detailSuccessText" class="text-gray-500"><?php
            echo explode('-', $success)[1]; ?></p>
          </div>
        </div>
        <button id="btnClose" class="text-gray-600 hover:bg-white/5 p-1 rounded-md transition-colors ease-linear">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
    <?php

  } else {
    ?>
    <!-- From Uiverse.io by seyed-mohsen-mousavi -->
    <div id="alertDiv" class="flex flex-col gap-2 w-60 sm:w-72 text-[10px] sm:text-xs z-50">
      <div
        class="error-alert cursor-default flex items-center justify-between w-full h-12 sm:h-14 rounded-lg bg-[#232531] px-[10px]">
        <div class="flex gap-2">
          <div class="text-[#d65563] bg-white/5 backdrop-blur-xl p-1 rounded-lg">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"></path>
            </svg>
          </div>
          <div>
            <p id="headErrorText" class="text-white"><?php
            echo explode('-', $error)[0]; ?> </p>
            <p id="detailErrorText" class="text-gray-500"><?php
            echo explode('-', $error)[1]; ?></p>
          </div>
        </div>
        <button id="btnClose" class="text-gray-600 hover:bg-white/10 p-1 rounded-md transition-colors ease-linear">
          <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <?php
  }
}