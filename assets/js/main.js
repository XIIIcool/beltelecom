$( document ).ready(function() {

    var path = 'http://pic-post.ru/beltelecom/api/';

    //регистрация
    $(document).on('submit','#form-reg',function(e){

        $.ajax({
            type: "POST",
            dataType: "json",
            data: {'email':$('#form-reg input[name="email"]').val(),'password':$('#form-reg input[name="password"]').val()},
            url: path+'user/register/',
            success: function(data){
                if(data.status == true){
                    alert('Регистрация прошла успешно');
                    var token = data.token;

                    console.log(token);
                    $.cookie('token', token, {
                        expires: 5,
                        path: '/',
                    });

                    window.location = 'http://pic-post.ru/beltelecom/main/';


                } else {
                    alert('Ошибка при регистрации: '+data.message);
                }
            }
        });

        return false;
    })
    // Авторизация
    $(document).on('submit','#form-auth',function(e){

        $.ajax({
            type: "POST",
            dataType: "json",
            data: {'email':$('#form-auth input[name="email"]').val(),'password':$('#form-auth input[name="password"]').val()},
            url: path+'user/login/',
            success: function(data){
                if(data.status == true) {
                    alert('Авторизация успешно');
                    var token = data.token;

                    console.log(token);
                    $.cookie('token', token, {
                        expires: 5,
                        path: '/',
                    });

                    window.location = 'http://pic-post.ru/beltelecom/main/';
                }
            },
            error: function(req, status, err) {
            console.log('Something went wrong', status, err);
            alert('Ошибка, Авторизации, проверьте введённые данные');
        }
        });

        return false;
    })
    //Добавление комментария
    $(document).on('submit','#commentAdd',function (e) {

        $.ajax({
            type: "POST",
            dataType: "json",
            data: {'post_id':$('#commentAdd input[name="post_id"]').val(),'body':$('#commentAdd textarea[name="body"]').val()},
            //contentType: "application/json",
            url: path+'comment/',
            crossDomain: true,
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + $.cookie('token'))
            },
            success: function(result) {
                if(result.status == true){
                    alert('Комментарий успешно отправлен, будет показан после проверки модератором');
                    location.reload();
                }
            },
            error: function(req, status, err) {
                console.log('Something went wrong', status, err);
            }
        });
        return false;
    })
    // Добавление поста
    $(document).on('submit','#postAdd',function (e) {

        $.ajax({
            type: "POST",
            dataType: "json",
            data: {'title':$('#postAdd input[name="title"]').val(),'body':$('#postAdd textarea[name="body"]').val()},
            //contentType: "application/json",
            url: path+'post/',
            crossDomain: true,
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + $.cookie('token'))
            },
            success: function(result) {
               // console.log(result);
                if(result.status == true){
                    alert('Пост успешно добавлен!');
                    location.reload();
                }
            },
            error: function(req, status, err) {
                console.log('Something went wrong', status, err);
            }
        });
        return false;
    })
    // Редактирование поста
    $(document).on('submit','#editpost',function (e) {

        $.ajax({
            type: "PATCH",
            dataType: "json",
            data: { //'id':$('#editpost input[name="id"]').val(),
                    'title':$('#editpost input[name="title"]').val(),
                    'body':$('#editpost textarea[name="body"]').val()},
            //contentType: "application/json",
            url: path+'post/'+$('#editpost input[name="id"]').val(),
            crossDomain: true,
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + $.cookie('token'))
            },
            success: function(result) {
                 console.log(result);
                if(result.status == true){
                    alert('Пост успешно сохранён!');
                    location.reload();
                }
            },
            error: function(req, status, err) {
                console.log('Something went wrong', status, err);
                alert('Ошибка, Не достаточно прав');
            }
        });
        return false;
    })
    // Удаление поста
    $(document).on('click','.deleteitem',function (e) {
        var element = this;
        $.ajax({
            type: "DELETE",
            dataType: "json",
            //data: { }
            //contentType: "application/json",
            url: path+'post/'+$(this).attr('data-id'),
            crossDomain: true,
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + $.cookie('token'))
            },
            success: function(result) {
                console.log(result);
                if(result.status == true){
                    alert('Пост успешно удалён!');
                    $(element).closest('div.postitem').hide('slow');
                }
            },
            error: function(req, status, err) {
                console.log('Something went wrong', status, err);
                alert('Ошибка, Не достаточно прав');
            }
        });
        return false;
    })

    //Удаление комментария
    $(document).on('click','.delete-comment a',function (e) {
        var element = this;
        $.ajax({
            type: "DELETE",
            dataType: "json",
            //data: { }
            //contentType: "application/json",
            url: path+'comment/'+$(this).attr('data-id'),
            crossDomain: true,
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + $.cookie('token'))
            },
            success: function(result) {
                console.log(result);
                if(result.status == true){
                    alert('Комментарий успешно удалён!');
                    $(element).closest('li.media').hide('slow');
                }
            },
            error: function(req, status, err) {
                console.log('Something went wrong', status, err);
                alert('Ошибка, Не достаточно прав');
            }
        });
        return false;
    })
    //Редактирование комментария открыть блок
    $(document).on('click','.edit-comment a',function (e) {
        var element = this;
        // console.log( $(element).closest('div.media-body').find('div.ec'));
        $(element).closest('div.media-body').find('div.ec').toggleClass('d-none');


        return false;
    })
    //Редактирование комментария
    $(document).on('click','button.update-comment',function (e) {
        var element = this;
        // console.log( $(element).closest('div.media-body').find('div.ec'));
        var id = $(this).attr('data-id');
        var body = $(this).closest('div.ec').find('textarea').val();
        console.log(body);

           $.ajax({
               type: "PATCH",
               dataType: "json",
               data: {'body': body },
               //contentType: "application/json",
               url: path+'comment/'+id,
               crossDomain: true,
               beforeSend: function(xhr) {
                   xhr.setRequestHeader("Authorization", "Bearer " + $.cookie('token'))
               },
               success: function(result) {
                   console.log(result);
                   if(result.status == true){
                       alert('Комментарий успешно Обновлен!');
                       $(element).closest('div.ec').hide('slow');
                       $(element).closest('div.media-body').find('div.media-text').html(body);
                   }
               },
               error: function(req, status, err) {
                   console.log('Something went wrong', status, err);
                   alert('Ошибка, Не достаточно прав');
               }
           });


        return false;
    })
    // Разрешение комментария
    $(document).on('click','.change-comment a',function (e) {

        var element = this;
        // console.log( $(element).closest('div.media-body').find('div.ec'));
        var id = $(this).attr('data-id');
        var datachange = $(this).attr('data-checked');

        $.ajax({
            type: "PATCH",
            dataType: "json",

            //contentType: "application/json",
            url: path+'comment/change/'+id,
            crossDomain: true,
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + $.cookie('token'))
            },
            success: function(result) {
                console.log(result);
                if(result.status == true){
                    alert('Успешно!');
                    if(datachange == 0){
                        $(element).html('Не показывать');
                    } else {
                        $(element).html('Показывать');
                    }
                   // $(element).closest('div.ec').hide('slow');
                   // $(element).closest('div.media-body').find('div.media-text').html(body);
                }
            },
            error: function(req, status, err) {
                console.log('Something went wrong', status, err);
                alert('Ошибка, Не достаточно прав');
            }
        });


        return false;
    })

});

function logout(){
    $.cookie('token', null,{
        expires: 0,
        path: '/',
    });
    location.reload();
}