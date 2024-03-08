��    =        S   �      8  �   9     �     �  ,   �  	             %     9  !   F     h  
   t          �     �  &   �  ,   �          &     3  #   ?     c     r  S     M   �     !  
   *  
   5  -   @  +   n     �     �     �     �  3   �     	  !   	  �   0	  h   �	  �   !
  �   �
     �     �  %   �  �   �  2   �  0  �               "  	   )     3     D  �   d  6   �  �   #  #  �     �     �       O     +  n  �   �     \     k  N   r     �     �  &   �       ?   /     o     �      �     �     �  4   �  D        T     p  
   �  A   �  1   �  )     ~   +  _   �     
          0  J   B  J   �     �     �          &  S   B     �  2   �  �   �  �   �    l  *  �     �     �  <   �  .  %  S   T   �  �      J"     i"  
   v"     �"     �"  +   �"  �   �"  X   �#  �   $  �  �$     k&  &   �&     �&  b   �&     #   '                     0   "      	   
      -   4                /                    1   *      2                   !         (                                         <       )      3   %               5       .          ,   7   ;      9             $       &   6           8   =   +   :        %s <i class="fas fa-question-circle" data-toggle="tooltip" title="This field name is present in your ClickUP custom fields list with the defined Name."></i> API Key Action Add ClickUP List ID to send the form data to Add Field Add Form Add Gravity Form ID Add New Form Add Your ClickUP Personal API Key Amir Jamali Attachment Click Up Status Settings ClickUp Configuration ClickUp Status Plugin Configure your ClickUp settings below. Configure your Tracking Form settings below. Custom fields Delete Field Description Description to show in the frontend Email Field ID Email Field: Enable sending email to the user to track the status of their request for this form Enter the Field ID for the form where you want to save its data into Click UP Field ID Field Name Field Type Fields to send to ClickUP for the added forms Files type are either Attachment or Regular Form Configurations => Form ID Form ID: Form ID: %s Forms configuration overview and associated fields: GF Settings GF Tracking Code Field (optional) If the field already exists (add existing Field Name), adding it again will update the fields' data in case any changes have been made. If the form already exists, adding it again will update the settings in case any changes have been made. If you intend to save this field into Click UP custom fields, make sure to first enable the Custom Field option for the form, and second, enter the field name exactly matching the Custom Field name. If your ClickUP account supports custom fields, you can enable this feature. Ensure that your field names match the custom fields, or they will be added to the description section. List ID List ID: Mark the field as an attachment field Note: Please be aware that Attachment fields and Custom fields must be sent individually, necessitating one request for each file and each field. This is due to ClickUP not supporting arrays for these types. Select the form from the ones you have added here. Selecting this checkbox designates the field to be used as the Task Name in ClickUP. By default, the first field added to the form will be chosen as the Task Name. However, you can change it by selecting a different field when adding a new field. Ensure at least one field is designated as the Task Name. Send tracking code Status Submit Task Name Task Name Field: The entered values do not match This plugin enable the admin to connect Gravity Forms to Click UP and let the users to track the status of the form they have submitted To edit the Tracking Form you should first add a form. To include the tracking code with form entries for display in the form entries settings, consider adding a hidden field to your form and placing the ID there. To make the description dynamic, use the following variables: {{status}} and {{assignee}}. For instance, you can create a dynamic description like this: "Your status is {{status}} and is being investigated by {{assignee}}". These variables will be replaced with actual values when displayed. Tracking Form Tracking Form Settings Update Description You can define the description to display for each status in the tracking form. Project-Id-Version: ClickUp Status Plugin
POT-Creation-Date: 2024-03-07 15:46+0400
PO-Revision-Date: 2024-03-07 15:47+0400
Last-Translator: 
Language-Team: 
Language: ar
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 2.2
X-Poedit-Basepath: ..
Plural-Forms: nplurals=6; plural=(n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 && n%100<=99 ? 4 : 5);
X-Poedit-Flags-xgettext: --add-comments=translators:
X-Poedit-WPHeader: clickup-status-plugin.php
X-Poedit-SourceCharset: UTF-8
X-Poedit-KeywordsList: __;_e;_n:1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;esc_attr__;esc_attr_e;esc_attr_x:1,2c;esc_html__;esc_html_e;esc_html_x:1,2c;_n_noop:1,2;_nx_noop:3c,1,2;__ngettext_noop:1,2
X-Poedit-SearchPath-0: .
X-Poedit-SearchPathExcluded-0: *.js
 %s <i class="fas fa-question-circle" data-toggle="tooltip" title="هذا الاسم الحقل موجود في قائمة حقولك المخصصة في ClickUP بالاسم المحدد."></i> مفتاح API عمل أضف معرف ClickUP List لإرسال بيانات النموذج إليه إضافة حقل إضافة نموذج إضافة معرف نموذج Gravity إضافة نموذج جديد أضف مفتاح API الشخصي لـ ClickUP الخاص بك أمير جمالي المرفق إعدادات حالة Click Up تكوين ClickUp ClickUp Status Plugin قم بتكوين إعدادات ClickUp أدناه  قم بتكوين إعدادات نموذج التتبع أدناه. الحقول المخصصة حذف الحقل الوصف الوصف الذي يظهر في الواجهة الأمامية معرف حقل البريد الإلكتروني حقل البريد الإلكتروني: تمكين إرسال بريد إلكتروني إلى المستخدم لتتبع حالة طلبهم لهذا النموذج أدخل هوية الحقل للنموذج حيث تريد حفظ بياناته في Click UP هوية الحقل اسم الحقل نوع الحقل الحقول لإرسالها إلى ClickUP للنماذج المضافة يمكن أن يكون نوع الملفات إما ملحق أو عادي تكوين النماذج => معرف النموذج معرف النموذج: هوية النموذج: %s نظرة عامة على تكوين النماذج والحقول المرتبطة: إعدادات GF GF رمز تتبع النموذج (اختياري) إذا كان الحقل موجودًا بالفعل (أضف اسم الحقل الحالي) ، فإن إضافته مرة أخرى ستقوم بتحديث بيانات الحقول في حال أي تغييرات تمت. إذا كان النموذج موجودًا بالفعل ، فإن إضافته مرة أخرى ستقوم بتحديث الإعدادات في حال أي تغييرات تمت. إذا كنت تنوي حفظ هذا الحقل في حقول Click UP المخصصة ، تأكد من تمكين خيار الحقل المخصص للنموذج أولاً ، وثانيًا ، أدخل اسم الحقل بالضبط متطابقًا مع اسم الحقل المخصص. إذا كان حساب ClickUP الخاص بك يدعم الحقول المخصصة ، يمكنك تمكين هذه الميزة. تأكد من أن أسماء الحقول الخاصة بك تتطابق مع الحقول المخصصة ، وإلا سيتم إضافتها إلى قسم الوصف. معرف القائمة معرف القائمة: قم بوضع علامة على الحقل كحقل مرفق ملاحظة: يرجى أن تكون على علم بأن حقول المرفقات والحقول المخصصة يجب أن ترسل على حدة ، مما يستلزم طلبًا لكل ملف وكل حقل. يرجى العلم أن ClickUP لا يدعم المصفوفات لهذه الأنواع. حدد النموذج من بين النماذج التي قد أضفتها هنا. بتحديد هذا الخانة يتم استخدام الحقل كاسم المهمة في ClickUP. بشكل افتراضي ، سيتم اختيار أول حقل يتم إضافته إلى النموذج كاسم مهمة. ومع ذلك ، يمكنك تغييره عند اختيار حقل مختلف عند إضافة حقل جديد. تأكد من تحديد حقل واحد على الأقل كاسم مهمة. إرسال رمز التتبع الحالة إرسال اسم المهمة حقل اسم المهمة: القيم المدخلة لا تتطابق يمكن لهذا المكون الإضافي تمكين المسؤول من ربط Gravity Forms بـ Click UP والسماح للمستخدمين بتتبع حالة النموذج الذي قدموه لتحرير نموذج التتبع، يجب عليك أولاً إضافة نموذج. لتضمين رمز التتبع مع إدخالات النموذج لعرضها في إعدادات إدخال النموذج ، يرجى إضافة حقلًا مخفيًا إلى نموذجك ووضع الهوية هناك. لجعل الوصف ديناميكيًا ، استخدم المتغيرات التالية: {{status}} و {{assignee}}. على سبيل المثال ، يمكنك إنشاء وصف ديناميكي مثل هذا: "حالتك هي {{status}} وتمت المعاينة بواسطة {{assignee}}". سيتم استبدال هذه المتغيرات بالقيم الفعلية عند العرض. نموذج التتبع إعدادات نموذج التتبع تحديث الوصف يمكنك تحديد الوصف الذي سيظهر لكل حالة في نموذج التتبع. 