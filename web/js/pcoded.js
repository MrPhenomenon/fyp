var elem = document.querySelectorAll(".pc-sidebar .pc-navbar a");
for (var l = 0; l < elem.length; l++) {
  var pageUrl = window.location.href.split(/[?#]/)[0];
  if (elem[l].href == pageUrl && elem[l].getAttribute("href") != "") {
    elem[l].parentNode.classList.add("active");

    elem[l].parentNode.parentNode.parentNode.classList.add("pc-trigger");
    elem[l].parentNode.parentNode.parentNode.classList.add("active");
    elem[l].parentNode.parentNode.style.display = "block";

    elem[
      l
    ].parentNode.parentNode.parentNode.parentNode.parentNode.classList.add(
      "pc-trigger",
    );
    elem[l].parentNode.parentNode.parentNode.parentNode.style.display = "block";
  }
}

function removeClassByPrefix(node, prefix) {
  for (let i = 0; i < node.classList.length; i++) {
    let value = node.classList[i];
    if (value.startsWith(prefix)) {
      node.classList.remove(value);
    }
  }
}

let slideUp = (target, duration = 0) => {
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.boxSizing = "border-box";
  target.style.height = target.offsetHeight + "px";
  target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
};

let slideDown = (target, duration = 0) => {
  target.style.removeProperty("display");
  let display = window.getComputedStyle(target).display;

  if (display === "none") display = "block";

  target.style.display = display;
  let height = target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  target.offsetHeight;
  target.style.boxSizing = "border-box";
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.height = height + "px";
  target.style.removeProperty("padding-top");
  target.style.removeProperty("padding-bottom");
  target.style.removeProperty("margin-top");
  target.style.removeProperty("margin-bottom");
  window.setTimeout(() => {
    target.style.removeProperty("height");
    target.style.removeProperty("overflow");
    target.style.removeProperty("transition-duration");
    target.style.removeProperty("transition-property");
  }, duration);
};

var slideToggle = (target, duration = 0) => {
  if (window.getComputedStyle(target).display === "none") {
    return slideDown(target, duration);
  } else {
    return slideUp(target, duration);
  }
};

$(document).on({
  ajaxSend: function (event, jqXHR, settings) {
    if (!settings._excludeFromGlobalLoader) {
      showloader();
    }
  },
  ajaxComplete: function (event, jqXHR, settings) {
    if (!settings._excludeFromGlobalLoader) {
      hideloader();
    }
  },
});

function showloader() {
  $("#global-loader").fadeIn();
}

function hideloader() {
  $("#global-loader").fadeOut();
}

$(document).on("click", "a", function (e) {
  const href = $(this).attr("href");

  if (!href) return;
  if (
    href === "#" ||
    href.startsWith("#") ||
    href.startsWith("javascript:") ||
    this.className.includes("no-loader")
  )
    return;

  showloader();
});

$(document).on("submit", "form", function () {
  showloader();
});

$(window).on("load", function () {
  hideloader();
});

function showToast(message, type = "success") {
  const $toast = $("#global-toast");
  const $body = $("#global-toast-body");

  $body.text(message);

  $toast
    .removeClass("bg-success bg-danger bg-warning bg-info")
    .addClass(`bg-${type}`);

  const toast = new bootstrap.Toast($toast[0]);
  toast.show();
}

let deleteTargetId = null;
let deleteEndpoint = null;

$(document).on("click", ".btn-delete", function () {
  const modalbody = $("#deleteConfirmModal .modal-body");
  deleteTargetId = $(this).data("id");
  deleteEndpoint = $(this).data("url");
  const itemLabel = $(this).data("item") || "this item";

  modalbody.html(
    ` <strong> You're deleting ${itemLabel}</strong>. Are you sure you want to continue?`,
  );
  const modal = new bootstrap.Modal(
    document.getElementById("deleteConfirmModal"),
  );
  modal.show();
});

$("#confirm-delete-btn").on("click", function () {
  if (!deleteTargetId || !deleteEndpoint) return;

  $.ajax({
    type: "POST",
    url: deleteEndpoint,
    data: { id: deleteTargetId },
    success: function (res) {
      if (res.success) {
        showToast(res.message || "Deleted successfully.", "success");
        $(`[data-id="${deleteTargetId}"]`).closest("tr").remove();
      } else {
        showToast(res.message || "Delete failed.", "danger");
      }
    },
    error: function () {
      showToast("Server error during delete.", "danger");
    },
    complete: function () {
      const modalEl = document.getElementById("deleteConfirmModal");
      const modal = bootstrap.Modal.getInstance(modalEl);
      modal.hide();
    },
  });
});

function updateSidebarState() {
  const sidebar = document.querySelector(".pc-sidebar");
  const header = document.querySelector(".pc-header");

  if (window.innerWidth <= 1024) {
    sidebar.classList.add("pc-sidebar-hide");
    sidebar.classList.remove("sidebar-open");
    header.classList.add("collapsed");
  } else {
    sidebar.classList.remove("sidebar-open");
    header.classList.remove("collapsed");
    sidebar.classList.remove("pc-sidebar-hide");
  }
}
updateSidebarState();

function closeSidebarMobile() {
  $("nav.pc-sidebar").removeClass("pc-sidebar-show");
  $("header").removeClass("collapsed");
}

$("#sidebarToggle").on("click", function () {
  const $sidebar = $("nav.pc-sidebar");
  const $header = $("header");

  if (window.innerWidth < 992) {
    $sidebar.toggleClass("pc-sidebar-show");
    $header.toggleClass("collapsed");
  } else {
    $sidebar.toggleClass("pc-sidebar-hide");
    $header.toggleClass("collapsed");
  }
});

// Close sidebar on mobile via overlay tap or close button
$("#sidebarOverlay, #sidebarClose").on("click", function () {
  closeSidebarMobile();
});
