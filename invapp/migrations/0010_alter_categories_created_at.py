# Generated by Django 5.1.7 on 2025-03-28 06:16

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('invapp', '0009_categories_custom_code'),
    ]

    operations = [
        migrations.AlterField(
            model_name='categories',
            name='created_at',
            field=models.DateTimeField(auto_now_add=True),
        ),
    ]
