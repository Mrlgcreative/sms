/**
 * JavaScript pour la gestion des événements scolaires avec mise à jour temps réel
 */

$(function () {
  // Variables globales
  var evenementsData = [];
  var baseUrl = '';
  var currentUsername = '';
  var calendar;
  
  // Récupérer les variables depuis le HTML
  try {
    if (typeof evenementsCalendar !== 'undefined') {
      evenementsData = JSON.parse(evenementsCalendar);
    }
    if (typeof BASE_URL !== 'undefined') {
      baseUrl = BASE_URL;
    }
    if (typeof username !== 'undefined') {
      currentUsername = username;
    }
  } catch (e) {
    console.error('Erreur lors du parsing des données:', e);
    evenementsData = [];
  }

  console.log('Données du calendrier:', evenementsData);

  // Initialisation de la DataTable
  if ($('#evenements-table').length > 0) {
    var dataTable = $('#evenements-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'responsive'  : true,
      'language'    : {
        'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
      },
      'columnDefs': [
        { 'orderable': false, 'targets': 5 }
      ]
    });
  }
  
  /* Initialisation des événements externes */
  function init_events(ele) {
    ele.each(function () {
      var eventObject = {
        title: $.trim($(this).text())
      }
      $(this).data('eventObject', eventObject)
      $(this).draggable({
        zIndex        : 1070,
        revert        : true,
        revertDuration: 0
      })
    })
  }

  init_events($('#external-events div.external-event'))

  /* Initialisation du calendrier */
  if ($('#calendar').length > 0) {
    calendar = $('#calendar').fullCalendar({
      header    : {
        left  : 'prev,next today',
        center: 'title',
        right : 'month,agendaWeek,agendaDay,listWeek'
      },
      buttonText: {
        today: 'Aujourd\'hui',
        month: 'Mois',
        week : 'Semaine',
        day  : 'Jour',
        listWeek: 'Liste'
      },
      locale: 'fr',
      events: evenementsData,
      editable  : true,
      droppable : true,
      height    : 'auto',
      aspectRatio: 1.8,
      defaultView: 'month',
      
      // Événement lors du drop d'un élément externe
      drop: function (date, allDay) {
        var originalEventObject = $(this).data('eventObject')
        var copiedEventObject = $.extend({}, originalEventObject)
        copiedEventObject.start = date
        copiedEventObject.allDay = allDay || false
        copiedEventObject.backgroundColor = $(this).css('background-color')
        copiedEventObject.borderColor = $(this).css('border-color')

        // Ajouter l'événement visuellement au calendrier
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

        // Ajouter l'événement en base de données
        var eventData = {
          titre: copiedEventObject.title,
          date_debut: moment(date).format('YYYY-MM-DD HH:mm:ss'),
          date_fin: moment(date).add(1, 'hour').format('YYYY-MM-DD HH:mm:ss'),
          couleur: rgbToHex(copiedEventObject.backgroundColor),
          lieu: 'À déterminer',
          responsable: currentUsername,
          description: ''
        };

        // Sauvegarder en base de données
        $.ajax({
          url: baseUrl + 'index.php?controller=Director&action=ajouterEvenementRapide',
          type: 'POST',
          data: eventData,
          dataType: 'json',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response && response.success) {
              // Mettre à jour l'événement avec l'ID de la base de données
              copiedEventObject.id = response.event_id;
              $('#calendar').fullCalendar('removeEvents', function(event) {
                return event.title === copiedEventObject.title && 
                       !event.id && 
                       moment(event.start).isSame(moment(copiedEventObject.start));
              });
              $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
              
              showAlert('Événement "' + copiedEventObject.title + '" ajouté avec succès', 'success');
              
              // Ajouter à la table
              addEventToTable(response.calendar_event);
            } else {
              // Supprimer l'événement du calendrier en cas d'erreur
              $('#calendar').fullCalendar('removeEvents', function(event) {
                return event.title === copiedEventObject.title && 
                       moment(event.start).isSame(moment(copiedEventObject.start));
              });
              showAlert('Erreur lors de l\'ajout: ' + (response.message || 'Erreur inconnue'), 'danger');
            }
          },
          error: function() {
            // Supprimer l'événement du calendrier en cas d'erreur
            $('#calendar').fullCalendar('removeEvents', function(event) {
              return event.title === copiedEventObject.title && 
                     moment(event.start).isSame(moment(copiedEventObject.start));
            });
            showAlert('Erreur de connexion au serveur', 'danger');
          }
        });

        if ($('#drop-remove').is(':checked')) {
          $(this).remove()
        }
      },
      
      // Redimensionnement d'un événement
      eventResize: function(event, delta, revertFunc) {
        updateEventInDatabase(event, revertFunc);
      },
      
      // Déplacement d'un événement
      eventDrop: function(event, delta, revertFunc) {
        updateEventInDatabase(event, revertFunc);
      },
      
      // Clic sur un événement
      eventClick: function(calEvent, jsEvent, view) {
        if (calEvent.id) {
          loadEventDetails(calEvent.id);
        }
      },
      
      // Erreur de chargement
      eventRenderError: function(error) {
        console.error('Erreur de rendu du calendrier:', error);
      }
    });
    
    console.log('Calendrier initialisé avec', evenementsData.length, 'événements');
  }

  // ========================= FONCTIONS UTILITAIRES =========================

  /**
   * Convertir RGB en HEX
   */
  function rgbToHex(rgb) {
    if (!rgb || rgb.indexOf('rgb') === -1) return rgb;
    
    var result = rgb.match(/\d+/g);
    if (!result || result.length < 3) return '#3c8dbc';
    
    return "#" + parseInt(result[0]).toString(16).padStart(2, '0') + 
                 parseInt(result[1]).toString(16).padStart(2, '0') + 
                 parseInt(result[2]).toString(16).padStart(2, '0');
  }

  /**
   * Mettre à jour un événement dans la base de données après modification dans le calendrier
   */
  function updateEventInDatabase(event, revertFunc) {
    var eventData = {
      event_id: event.id,
      titre: event.title,
      date_debut: moment(event.start).format('YYYY-MM-DD HH:mm:ss'),
      date_fin: moment(event.end || event.start).format('YYYY-MM-DD HH:mm:ss'),
      lieu: event.location || 'À déterminer',
      responsable: event.responsible || currentUsername,
      description: event.description || '',
      couleur: event.backgroundColor || '#3c8dbc'
    };

    $.ajax({
      url: baseUrl + 'index.php?controller=Director&action=updateEvenement',
      type: 'POST',
      data: eventData,
      dataType: 'json',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response && response.success) {
          showAlert('Événement mis à jour avec succès', 'success');
          // Mettre à jour la table si nécessaire
          updateEventInTable(response.calendar_event);
        } else {
          showAlert('Erreur lors de la mise à jour: ' + (response.message || 'Erreur inconnue'), 'danger');
          revertFunc();
        }
      },
      error: function() {
        showAlert('Erreur de connexion au serveur', 'danger');
        revertFunc();
      }
    });
  }

  /**
   * Ajouter un événement à la table DataTable
   */
  function addEventToTable(event) {
    if (!dataTable) return;
    
    var startDate = moment(event.start).format('DD/MM/YYYY HH:mm');
    var endDate = moment(event.end).format('DD/MM/YYYY HH:mm');
    
    var newRow = [
      '<strong>' + escapeHtml(event.title) + '</strong>',
      '<span class="label label-primary"><i class="fa fa-clock-o"></i> ' + startDate + '</span>',
      '<span class="label label-warning"><i class="fa fa-clock-o"></i> ' + endDate + '</span>',
      '<i class="fa fa-map-marker text-red"></i> ' + escapeHtml(event.location || 'À déterminer'),
      '<i class="fa fa-user text-blue"></i> ' + escapeHtml(event.responsible || currentUsername),
      '<div class="btn-group">' +
        '<button class="btn btn-xs btn-info view-event" data-id="' + event.id + '" title="Voir les détails">' +
          '<i class="fa fa-eye"></i>' +
        '</button>' +
        '<button class="btn btn-xs btn-warning btn-edit-event" data-id="' + event.id + '" title="Modifier">' +
          '<i class="fa fa-edit"></i>' +
        '</button>' +
        '<button class="btn btn-xs btn-danger btn-delete-event" data-id="' + event.id + '" data-title="' + escapeHtml(event.title) + '" title="Supprimer">' +
          '<i class="fa fa-trash"></i>' +
        '</button>' +
      '</div>'
    ];
    
    dataTable.row.add(newRow).draw();
  }

  /**
   * Mettre à jour un événement dans la table DataTable
   */
  function updateEventInTable(event) {
    if (!dataTable) return;
    
    // Rechercher et mettre à jour la ligne correspondante
    dataTable.rows().every(function(rowIdx, tableLoop, rowLoop) {
      var data = this.data();
      var $actionButtons = $(data[5]);
      var eventId = $actionButtons.find('.view-event').data('id');
      
      if (eventId == event.id) {
        var startDate = moment(event.start).format('DD/MM/YYYY HH:mm');
        var endDate = moment(event.end).format('DD/MM/YYYY HH:mm');
        
        var updatedRow = [
          '<strong>' + escapeHtml(event.title) + '</strong>',
          '<span class="label label-primary"><i class="fa fa-clock-o"></i> ' + startDate + '</span>',
          '<span class="label label-warning"><i class="fa fa-clock-o"></i> ' + endDate + '</span>',
          '<i class="fa fa-map-marker text-red"></i> ' + escapeHtml(event.location || 'À déterminer'),
          '<i class="fa fa-user text-blue"></i> ' + escapeHtml(event.responsible || currentUsername),
          data[5] // Garder les boutons d'action
        ];
        
        this.data(updatedRow).draw();
        return false; // Sortir de la boucle
      }
    });
  }

  /**
   * Supprimer un événement de la table DataTable
   */
  function removeEventFromTable(eventId) {
    if (!dataTable) return;
    
    dataTable.rows().every(function(rowIdx, tableLoop, rowLoop) {
      var data = this.data();
      var $actionButtons = $(data[5]);
      var currentEventId = $actionButtons.find('.view-event').data('id');
      
      if (currentEventId == eventId) {
        this.remove();
        return false; // Sortir de la boucle
      }
    });
    
    dataTable.draw();
  }

  // ========================= GESTION DES MODALES =========================

  // Modal d'ajout d'événement détaillé
  $('#btn-add-detailed-event').on('click', function() {
    openAddEventModal();
  });

  // Modal de modification d'événement
  $(document).on('click', '.btn-edit-event', function() {
    var eventId = $(this).data('id');
    openEditEventModal(eventId);
  });

  // Modal de suppression d'événement
  $(document).on('click', '.btn-delete-event', function(e) {
    e.preventDefault();
    var eventId = $(this).data('id');
    var eventTitle = $(this).data('title');
    openDeleteEventModal(eventId, eventTitle);
  });

  /**
   * Ouvrir la modal d'ajout d'événement
   */
  function openAddEventModal() {
    var modalHtml = `
      <div class="modal fade" id="modal-add-event" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">
                <i class="fa fa-plus-circle"></i> Ajouter un Nouvel Événement
              </h4>
            </div>
            <div class="modal-body">
              <form id="form-add-event">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><i class="fa fa-tag"></i> Titre *</label>
                      <input type="text" class="form-control" name="titre" required 
                             placeholder="Ex: Réunion des parents">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><i class="fa fa-map-marker"></i> Lieu *</label>
                      <input type="text" class="form-control" name="lieu" required 
                             placeholder="Ex: Salle de conférence">
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><i class="fa fa-calendar-o"></i> Date de début *</label>
                      <input type="datetime-local" class="form-control" name="date_debut" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><i class="fa fa-calendar"></i> Date de fin *</label>
                      <input type="datetime-local" class="form-control" name="date_fin" required>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><i class="fa fa-user"></i> Responsable *</label>
                      <input type="text" class="form-control" name="responsable" 
                             value="${currentUsername}" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><i class="fa fa-palette"></i> Couleur</label>
                      <select class="form-control" name="couleur">
                        <option value="#3c8dbc" style="background-color: #3c8dbc; color: white;">Bleu</option>
                        <option value="#00a65a" style="background-color: #00a65a; color: white;">Vert</option>
                        <option value="#f39c12" style="background-color: #f39c12; color: white;">Orange</option>
                        <option value="#dd4b39" style="background-color: #dd4b39; color: white;">Rouge</option>
                        <option value="#605ca8" style="background-color: #605ca8; color: white;">Violet</option>
                        <option value="#00c0ef" style="background-color: #00c0ef; color: white;">Aqua</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label><i class="fa fa-file-text-o"></i> Description</label>
                  <textarea class="form-control" name="description" rows="3" 
                            placeholder="Description détaillée de l'événement..."></textarea>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> Annuler
              </button>
              <button type="button" class="btn btn-primary" id="btn-save-add-event">
                <i class="fa fa-save"></i> Enregistrer
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Supprimer la modal existante si elle existe
    $('#modal-add-event').remove();
    
    // Ajouter la nouvelle modal au DOM
    $('body').append(modalHtml);
    
    // Afficher la modal
    $('#modal-add-event').modal('show');
    
    // Gérer la sauvegarde
    $('#btn-save-add-event').off('click').on('click', function() {
      saveAddEvent();
    });
  }

  /**
   * Sauvegarder un nouvel événement
   */
  function saveAddEvent() {
    var formData = $('#form-add-event').serializeArray();
    var formObject = {};
    
    // Convertir les données du formulaire en objet
    $.each(formData, function(i, field) {
      formObject[field.name] = field.value;
    });
    
    console.log('Données à envoyer:', formObject); // Debug
    
    // Validation côté client
    if (!formObject.titre || formObject.titre.trim() === '') {
      showAlert('Le titre est requis', 'warning');
      return;
    }
    
    if (!formObject.lieu || formObject.lieu.trim() === '') {
      showAlert('Le lieu est requis', 'warning');
      return;
    }
    
    if (!formObject.date_debut || !formObject.date_fin) {
      showAlert('Les dates de début et de fin sont requises', 'warning');
      return;
    }
    
    if (!formObject.responsable || formObject.responsable.trim() === '') {
      showAlert('Le responsable est requis', 'warning');
      return;
    }
    
    if (new Date(formObject.date_fin) <= new Date(formObject.date_debut)) {
      showAlert('La date de fin doit être postérieure à la date de début', 'warning');
      return;
    }
    
    var $btn = $('#btn-save-add-event');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');
    
    $.ajax({
      url: baseUrl + 'index.php?controller=Director&action=ajouterEvenement',
      type: 'POST',
      data: formObject,
      dataType: 'json',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        console.log('Réponse du serveur:', response); // Debug
        if (response && response.success) {
          $('#modal-add-event').modal('hide');
          showAlert('Événement ajouté avec succès', 'success');
          
          // Ajouter l'événement au calendrier
          if (response.calendar_event) {
            $('#calendar').fullCalendar('renderEvent', response.calendar_event, true);
            
            // Ajouter à la table
            addEventToTable(response.calendar_event);
          }
        } else {
          showAlert('Erreur lors de l\'ajout: ' + (response.message || 'Erreur inconnue'), 'danger');
        }
      },
      error: function(xhr, status, error) {
        console.error('Erreur AJAX:', xhr.responseText); // Debug
        try {
          var response = JSON.parse(xhr.responseText);
          showAlert('Erreur: ' + (response.message || error), 'danger');
        } catch(e) {
          showAlert('Erreur de connexion au serveur: ' + error, 'danger');
        }
      },
      complete: function() {
        $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
      }
    });
  }

  /**
   * Ouvrir la modal de suppression d'événement
   */
  function openDeleteEventModal(eventId, eventTitle) {
    var modalHtml = `
      <div class="modal fade" id="modal-delete-event" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-danger">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">
                <i class="fa fa-warning"></i> Confirmation de Suppression
              </h4>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger">
                <h4><i class="fa fa-exclamation-triangle"></i> Attention!</h4>
                <p>Êtes-vous sûr de vouloir supprimer l'événement :</p>
                <p><strong>"${escapeHtml(eventTitle)}"</strong></p>
                <p><em>Cette action est irréversible.</em></p>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> Annuler
              </button>
              <button type="button" class="btn btn-danger" id="btn-confirm-delete" data-id="${eventId}">
                <i class="fa fa-trash"></i> Supprimer Définitivement
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Fermer les autres modales
    $('.modal').modal('hide');
    
    // Supprimer et ajouter la nouvelle modal
    $('#modal-delete-event').remove();
    $('body').append(modalHtml);
    $('#modal-delete-event').modal('show');
    
    // Gérer la confirmation
    $('#btn-confirm-delete').off('click').on('click', function() {
      var id = $(this).data('id');
      deleteEvent(id);
    });
  }

  /**
   * Supprimer un événement
   */
  function deleteEvent(eventId) {
    var $btn = $('#btn-confirm-delete');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Suppression...');
    
    $.ajax({
      url: baseUrl + 'index.php?controller=Director&action=supprimerEvenement',
      type: 'POST',
      data: {id: eventId},
      dataType: 'json',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response && response.success) {
          $('#modal-delete-event').modal('hide');
          showAlert('Événement supprimé avec succès', 'success');
          
          // Supprimer du calendrier
          $('#calendar').fullCalendar('removeEvents', eventId);
          
          // Supprimer de la table
          removeEventFromTable(eventId);
        } else {
          showAlert('Erreur lors de la suppression: ' + (response.message || 'Erreur inconnue'), 'danger');
        }
      },
      error: function() {
        showAlert('Erreur de connexion au serveur', 'danger');
      },
      complete: function() {
        $btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Supprimer Définitivement');
      }
    });
  }

  // ========================= FONCTIONS UTILITAIRES =========================

  /**
   * Échapper le HTML pour éviter les injections XSS
   */
  function escapeHtml(text) {
    if (!text) return '';
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }

  /**
   * Formater une date pour les inputs datetime-local
   */
  function formatDateForInput(dateString) {
    if (!dateString) return '';
    var date = new Date(dateString);
    var year = date.getFullYear();
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var day = String(date.getDate()).padStart(2, '0');
    var hours = String(date.getHours()).padStart(2, '0');
    var minutes = String(date.getMinutes()).padStart(2, '0');
    return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
  }

  /**
   * Afficher des alertes stylées
   */
  function showAlert(message, type) {
    var alertClass = 'alert-' + type;
    var iconClass = type === 'success' ? 'check' : (type === 'warning' ? 'warning' : 'exclamation-triangle');
    var title = type === 'success' ? 'Succès!' : (type === 'warning' ? 'Attention!' : 'Erreur!');
    
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible alert-floating" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '<strong><i class="fa fa-' + iconClass + '"></i> ' + title + '</strong> ' + message + '</div>';
    
    $('body').append(alertHtml);
    
    setTimeout(function() {
      $('.alert-floating').fadeOut(function() {
        $(this).remove();
      });
    }, 4000);
  }

  // ========================= GESTION DES ÉVÉNEMENTS RAPIDES =========================

  var currColor = '#3c8dbc'
  $('#color-chooser > li > a').click(function (e) {
    e.preventDefault()
    currColor = $(this).css('color')
    $('#add-new-event').css({
      'background-color': currColor,
      'border-color'    : currColor
    }).addClass('animated pulse')
    
    setTimeout(function() {
      $('#add-new-event').removeClass('animated pulse')
    }, 600)
  })
  
  // Ajout d'événement rapide
  $('#add-new-event').click(function (e) {
    e.preventDefault()
    var val = $('#new-event').val().trim()
    
    if (val.length == 0) {
      $('#new-event').focus().addClass('animated shake')
      setTimeout(function() {
        $('#new-event').removeClass('animated shake')
      }, 600)
      return
    }

    // Créer l'élément visuel
    var event = $('<div />')
    event.css({
      'background-color': currColor,
      'border-color'    : currColor,
      'color'           : '#fff',
      'opacity'         : '0'
    }).addClass('external-event')
    event.html('<i class="fa fa-tag"></i> ' + val)
    $('#external-events').prepend(event)
    
    event.animate({opacity: 1}, 300)
    init_events(event)

    showAlert('Événement "' + val + '" ajouté aux événements suggérés', 'success');
    $('#new-event').val('').focus()
  })

  // Fonction pour charger les détails d'un événement (modal rapide)
  function loadEventDetails(eventId) {
    $.ajax({
      url: baseUrl + 'index.php?controller=Director&action=getEvenementDetails',
      type: 'GET',
      data: {id: eventId},
      dataType: 'json',
      success: function(response) {
        if(response && response.success) {
          var event = response.data;
          $('#event_id').val(event.id);
          $('#event_title').val(event.titre);
          $('#event_start').val(formatDateForInput(event.date_debut));
          $('#event_end').val(formatDateForInput(event.date_fin));
          $('#event_location').val(event.lieu);
          $('#event_responsible').val(event.responsable);
          $('#event_description').val(event.description);
          $('#modal-event-details').modal('show');
        } else {
          showAlert('Erreur lors de la récupération des détails', 'danger');
        }
      },
      error: function() {
        showAlert('Erreur de connexion au serveur', 'danger');
      }
    });
  }

  // Gestion des boutons "Voir"
  $(document).on('click', '.view-event', function() {
    var eventId = $(this).data('id');
    loadEventDetails(eventId);
  });

  // Enregistrer les modifications dans la modal rapide
  $('#save-event').on('click', function() {
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');
    
    $.ajax({
      url: baseUrl + 'index.php?controller=Director&action=updateEvenement',
      type: 'POST',
      data: $('#event-form').serialize(),
      dataType: 'json',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if(response && response.success) {
          $('#modal-event-details').modal('hide');
          showAlert('Événement modifié avec succès', 'success');
          
          // Mettre à jour le calendrier
          if (response.calendar_event) {
            $('#calendar').fullCalendar('removeEvents', response.calendar_event.id);
            $('#calendar').fullCalendar('renderEvent', response.calendar_event, true);
            
            // Mettre à jour la table
            updateEventInTable(response.calendar_event);
          }
        } else {
          showAlert('Erreur lors de la mise à jour: ' + (response.message || 'Erreur inconnue'), 'danger');
        }
      },
      error: function() {
        showAlert('Erreur de connexion au serveur', 'danger');
      },
      complete: function() {
        $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer les modifications');
      }
    });
  });

  // Initialisation des tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // Auto-resize du calendrier lors du redimensionnement de la fenêtre
  $(window).on('resize', function() {
    $('#calendar').fullCalendar('option', 'height', 'auto');
  });

  // Exposer les fonctions globalement pour pouvoir les appeler depuis les boutons HTML
  window.openAddEventModal = openAddEventModal;
  window.openDeleteEventModal = openDeleteEventModal;
});