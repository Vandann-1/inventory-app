# Generated by Django 5.1.7 on 2025-03-30 15:22

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('invapp', '0003_alter_subcategory_created_at_and_more'),
    ]

    operations = [
        migrations.AlterField(
            model_name='subcategory',
            name='category_code',
            field=models.CharField(default='DEFAULT_CODE', max_length=500),
        ),
    ]
