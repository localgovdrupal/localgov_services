/**
 * @file
 *   Drag and Drop addition to Landing forms.
 *
 * @see \Drupal\localgov_services_navigation\EntityChildRelationshipUi
 *
 * Drag from the listed children that are not referenced by the landing page
 * yet, to entity reference and link fields, to auto populate them.
 *
 * Manually tested with Claro and Stark.
 *
 * Uses basic HTML Drag and Drop so will work on most desktop, but not mobile.
 * For most draggable core uses deprecated jquery_ui draggable and new
 * SortableJS. SortableJS is for moving in and between lists. This changes
 * content for different field types, so for more compatibility maybe an
 * additional library would be required.
 */
(function localgovServiceChildScript(Drupal, once) {
  let dragChild;

  Drupal.behaviors.localgovServiceChild = {
    attach: function attach(context) {
      // Add draggability to child items.
      const children = once(
        'allServicesChildren',
        '.localgov-child-drag',
        context,
      );
      if (children) {
        children.forEach((child) => {
          child.setAttribute('draggable', true);
          child.classList.add('draggable');
          child.addEventListener('dragstart', (event) => {
            event.dataTransfer.dropEffect = 'move';
            event.dataTransfer.effectAllowed = 'move';
            dragChild = child;
          });
        });
      }
    },
  };

  Drupal.behaviors.localgovServiceTaskDrop = {
    attach: function attach(context) {
      // Is it always a table. Maybe form-item and then parent?
      const linkRows = once(
        'allServicesLinkRows',
        '[data-drupal-selector="edit-localgov-common-tasks"] tr',
        context,
      );
      linkRows.forEach((row) => {
        row.addEventListener('dragover', (event) => {
          const targetRow = event.target.closest('tr');
          const url = targetRow.querySelector(
            'input[data-drupal-selector$=uri]',
          );
          if (url.value === '') {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
          }
        });
        row.addEventListener('drop', (event) => {
          const targetRow = event.target.closest('tr');
          const url = targetRow.querySelector(
            'input[data-drupal-selector$=uri]',
          );
          if (url.value === '') {
            event.preventDefault();
            const title = targetRow.querySelector(
              'input[data-drupal-selector$=title]',
            );
            title.value = dragChild.getAttribute('data-localgov-title');
            url.value = dragChild.getAttribute('data-localgov-url');
            dragChild.remove();
          }
        });
      });
    },
  };

  Drupal.behaviors.localgovServiceChildDrop = {
    attach: function attach(context) {
      const linkRows = once(
        'allServicesLinkRows',
        '[data-drupal-selector="edit-localgov-destinations"] tr',
        context,
      );
      linkRows.forEach((row) => {
        row.addEventListener('dragover', (event) => {
          const ref = row.querySelector(
            'input[data-drupal-selector$=target-id]',
          );
          if (ref.value === '') {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
          }
        });
        row.addEventListener('drop', (event) => {
          const ref = row.querySelector(
            'input[data-drupal-selector$=target-id]',
          );
          if (ref.value === '') {
            event.preventDefault();
            ref.value = dragChild.getAttribute('data-localgov-reference');
            dragChild.remove();
          }
        });
      });
    },
  };

  Drupal.behaviors.localgovServiceSubChildDrop = {
    attach: function attach(context) {
      const linkRows = once(
        'allServicesLinkRows',
        '[data-drupal-selector$="-subform-topic-list-links"] tr',
        context,
      );
      linkRows.forEach((row) => {
        row.addEventListener('dragover', (event) => {
          const url = row.querySelector('input[data-drupal-selector$=uri]');
          if (url.value === '') {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
          }
        });
        row.addEventListener('drop', (event) => {
          const url = row.querySelector('input[data-drupal-selector$=uri]');
          if (url.value === '') {
            event.preventDefault();
            const title = row.querySelector(
              'input[data-drupal-selector$=title]',
            );
            title.value = dragChild.getAttribute('data-localgov-title');
            url.value = dragChild.getAttribute('data-localgov-url');
            dragChild.remove();
          }
        });
      });
    },
  };

  // Account for menus for sticky position.
  document.addEventListener('drupalViewportOffsetChange', () => {
    document.querySelector(
      'div.localgov-services-children-list.item-list',
    ).style.top = getComputedStyle(document.body).paddingTop;
  });
})(Drupal, once);
