# Generated by Django 5.1.7 on 2025-03-26 13:17

import invapp.models
from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('invapp', '0002_alter_customuser_mobile_no'),
    ]

    operations = [
        migrations.AddField(
            model_name='customuser',
            name='user_code',
            field=models.CharField(default=invapp.models.generate_random_text, max_length=500, unique=True),
        ),
    ]
